<?php

namespace App\Http\Controllers\Players;

use App\Http\Controllers\Controller;
use App\Models\Graph;
use App\Models\Player;
use App\Models\PlayerConfig;
use App\Models\PlayerIp;
use App\Models\PlayerConnection;
use App\Models\PlayerFreeze;
use App\Models\PlayerMute;
use App\Models\PlayerPunishment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class API extends Controller {

    public function lastBan() {
        return response()->json([
            'lastBan' => PlayerPunishment::where('created_at', '>', Carbon::now()->subMinutes(2))->with('player')->get(),
        ]);
    }

    public function stats() {
        $players = Player::with('bans', 'mutes', 'freezes', 'ips', 'logs')->get();
        $results = [];
        $players->each(function($item) use(&$results) {
            $timePlay = 0;
            foreach ($item->logs as $log) {
                if ($log->disconnect_at != null)
                    $timePlay += $log->created_at->diffInSeconds($log->disconnect_at);
                else {
                    $timePlay += $log->created_at->diffInSeconds(now());
                }
            }
            $results[] = [
                'player_uuid' => $item->uuid,
                'ips' => $item->ips->count(),
                'play_time' => Carbon::createFromTimestamp($timePlay)->format('H:i:s'),
                'connection_count' => $item->logs->count(),

            ];
        });
        return response()->json(
            $results
        );
    }

    public function list() {
        return response()->json([
            'players' => Player::all(),
        ]);
    }

    public function connected(Request $request) {
        if ($request->from != null) {
            $request->validate([
                'from' => 'required|date',
            ]);
            $from = PlayerConnection::where('connect_at', '>=', $request->from)->with('player')->get();
            if ($request->to != null) {
                $request->validate([
                    'to' => 'required|date',
                ]);
                $to = PlayerConnection::where('connect_at', '<=', $request->to)->with('player')->get();
                return response()->json([
                    'connected' => $from->intersect($to)->count(),
                ]);
            }
            return response()->json([
                'connected' => $from->count(),
            ]);
        }
        return response()->json([
            'connected' => PlayerConnection::whereNull('disconnect_at')->with('player')->get()->count(),
        ]);
    }

    public function find(Request $request) {
        $request->validate([
            'uuid' => 'required|uuid',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player === null) {
            return response()->json([
                'error' => 'Player not found',
            ], 404);
        }
        return response()->json([
            'player' => $player,
        ]);
    }

    public function checkPlayerStatus(Request $request, $statusCheckMethod) {
        $request->validate([
            'uuid' => 'required|uuid',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player === null) {
            return response()->json([
                'error' => 'Player not found',
            ], 404);
        }

        return $this->$statusCheckMethod($player);
    }

    public function isMute(Request $request) {
        return $this->checkPlayerStatus($request, 'playerIsMute');
    }

    public function isBan(Request $request) {
        return $this->checkPlayerStatus($request, 'playerIsBan');
    }

    public function isFreeze(Request $request) {
        return $this->checkPlayerStatus($request, 'playerIsFreeze');
    }

    public function info(Request $request) {
        return $this->checkPlayerStatus($request, 'playerInfo');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'uuid' => 'required|uuid',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player !== null) {
            return response()->json([
                'error' => 'Player already exists',
            ], 400);
        }

        $player = new Player();
        $player->name = $request->name;
        $player->uuid = $request->uuid;
        $player->save();

        return response()->json([
            'player' => $player,
        ]);
    }

    public function login(Request $request) {
        $request->validate([
            'uuid' => 'required|uuid',
            'name' => 'required|string',
            'ip' => 'required|ip',
        ]);

        $player = Player::where('uuid', $request->uuid)->with(["bans", "configs", "mutes", "freezes"])->first();
        if ($player === null) {
            $this->register($request);
            $player = Player::where('uuid', $request->uuid)->with(["bans", "configs", "mutes", "freezes"])->first();
        }

        $playerIp = PlayerIp::where('player_id', $player->id)->where('ip', $request->ip)->first();
        if ($playerIp === null) {
            $playerIp = new PlayerIp();
            $playerIp->player_id = $player->id;
            $playerIp->ip = $request->ip;
            $playerIp->save();
        }

        $connection = PlayerConnection::where('player_id', $player->id)->whereNull('disconnect_at')->first();
        if ($connection != null) {
            return response()->json([
                'error' => 'Player already connected',
            ], 400);
        }
        $connection = new PlayerConnection();
        $connection->player_id = $player->id;
        $connection->player_ip_id = $playerIp->id;
        $connection->connect_at = now();
        $connection->save();

        return response()->json([
            'player' => $player,
            'ip' => $playerIp,
            'connection' => $connection,
        ]);
    }

    public function logout(Request $request) {
        $request->validate([
            'uuid' => 'required|uuid',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player === null) {
            return response()->json([
                'error' => 'Player not found',
            ], 404);
        }

        $connection = PlayerConnection::where('player_id', $player->id)->whereNull('disconnect_at')->first();
        if ($connection === null) {
            return response()->json([
                'error' => 'Player not connected',
            ], 400);
        }

        $connection->disconnect_at = now();
        $connection->save();

        return response()->json([
            'connection' => $connection,
        ]);
    }

    public function punishPlayer(Request $request, $punishmentType) {
        $request->validate([
            'uuid' => 'required|uuid',
            'reason' => 'required|string',
            'punisher_uuid' => 'required|uuid',
            'duration' => 'required|integer',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player === null) {
            return response()->json([
                'error' => 'Player not found',
            ], 404);
        }
        $isPunished = null;

        $punishmentModel = ucfirst($punishmentType);
        if ($punishmentType === 'PlayerPunishment') {
            PlayerPunishment::where('player_id', $player->id)
            ->whereNull('deleted_at')
            ->first();
        } elseif ($punishmentType === 'PlayerMute') {
            PlayerMute::where('player_id', $player->id)
            ->whereNull('deleted_at')
            ->first();
        } elseif ($punishmentType === 'PlayerFreeze') {
            PlayerFreeze::where('player_id', $player->id)
            ->whereNull('deleted_at')
            ->first();
        }

        if ($isPunished !== null) {
            return response()->json([
                'error' => "Player already $punishmentType",
            ], 400);
        }

        $punisher = Player::where('uuid', $request->punisher_uuid)->first();
        if ($punisher === null) {
            return response()->json([
                'error' => 'Punisher not found',
            ], 404);
        }

        $allowedRoles = ['admin', 'dev', 'moderator', 'helper', 'builder'];
        if (!in_array($punisher->group->name, $allowedRoles)) {
            return response()->json([
                'error' => 'Punisher not allowed to punish',
            ], 403);
        }

        if (in_array($player->group->name, $allowedRoles)) {
            return response()->json([
                'error' => 'Player not punishable',
            ], 403);
        }

        if ($punishmentType === 'PlayerPunishment') {
            $punishment = new PlayerPunishment();
        } elseif ($punishmentType === 'PlayerMute') {
            $punishment = new PlayerMute();
        } elseif ($punishmentType === 'PlayerFreeze') {
            $punishment = new PlayerFreeze();
        }
        $punishment->player_id = $player->id;
        $punishment->punisher_id = $punisher->id;
        $punishment->reason = $request->reason;

        if ($request->duration > 0) {
            $punishment->deleted_at = now()->addSeconds($request->duration);
        }

        $punishment->save();

        return response()->json([
            strtolower($punishmentType) => $punishment,
        ]);
    }

    public function ban(Request $request) {
        return $this->punishPlayer($request, 'PlayerPunishment');
    }

    public function mute(Request $request) {
        return $this->punishPlayer($request, 'PlayerMute');
    }

    public function freeze(Request $request) {
        return $this->punishPlayer($request, 'PlayerFreeze');
    }

    private function unpunishPlayer(Request $request, $punishmentType) {
        $request->validate([
            'uuid' => 'required|uuid',
            'punisher_uuid' => 'required|uuid',
        ]);

        $player = Player::where('uuid', $request->uuid)->first();
        if ($player === null) {
            return response()->json([
                'error' => 'Player not found',
            ], 404);
        }

        $punishmentModel = 'Player' . ucfirst($punishmentType);
        $punisher = Player::where('uuid', $request->punisher_uuid)->first();
        if ($punisher === null) {
            return response()->json([
                'error' => 'Punisher not found',
            ], 404);
        }

        $allowedRoles = ['admin', 'dev', 'moderator', 'helper', 'builder'];
        if (!in_array($punisher->group, $allowedRoles)) {
            return response()->json([
                'error' => 'Punisher not allowed to punish',
            ], 403);
        }

        if (in_array($player->group, $allowedRoles)) {
            return response()->json([
                'error' => 'Player not punishable',
            ], 403);
        }

        $punishment = $punishmentModel::where('player_id', $player->id)
            ->whereNull('deleted_at')
            ->first();

        if ($punishment === null) {
            return response()->json([
                'error' => "Player not $punishmentType",
            ], 400);
        }

        $punishment->deleted_at = now();
        $punishment->save();

        return response()->json([
            strtolower($punishmentType) => $punishment,
        ]);
    }

    public function unban(Request $request) {
        return $this->unpunishPlayer($request, 'PlayerPunishment');
    }

    public function unmute(Request $request) {
        return $this->unpunishPlayer($request, 'PlayerMute');
    }

    public function unfreeze(Request $request) {
        return $this->unpunishPlayer($request, 'PlayerFreeze');
    }

    public function playerInfo(Player $player) {
        $player->load('mutes', 'bans', 'freezes', 'ips', 'configs');
        return response()->json([
            'player' => $player,
        ]);
    }

    public function playerIsBan(Player $player) {
        $player->load('bans');
        return response()->json([
            'isBan' => $player->bans->count() > 0,
        ]);
    }

    public function playerIsMute(Player $player) {
        $player->load('mutes');
        return response()->json([
            'isMute' => $player->mutes->count() > 0,
        ]);
    }

    public function playerIsFreeze(Player $player) {
        $player->load('freezes');
        return response()->json([
            'isFreeze' => $player->freezes->count() > 0,
        ]);
    }

    public function playerIsConnected(Player $player) {
        $connection = PlayerConnection::where('player_id', $player->id)->whereNull('disconnect_at')->first();
        return response()->json([
            'isConnected' => $connection !== null,
        ]);
    }

    public function playerStats(Player $player) {
        $player->load('bans', 'mutes', 'freezes', 'ips', 'logs');
        $timePlay = 0;
        foreach ($player->logs as $log) {
            if ($log->disconnect_at != null)
                $timePlay += $log->created_at->diffInSeconds($log->disconnect_at);
            else {
                $timePlay += $log->created_at->diffInSeconds(now());
            }
        }
        return response()->json([
            'bans' => $player->bans->count(),
            'mutes' => $player->mutes->count(),
            'freezes' => $player->freezes->count(),
            'ips' => $player->ips->count(),
            'play_time' => Carbon::createFromTimestamp($timePlay)->format('H:i:s'),
        ]);
    }

    public function playerConfigs(Player $player) {
        $player->load('configs');
        return response()->json([
            'configs' => $player->configs,
        ]);
    }

    public function playerSetGroup(Request $request, Player $player) {
        $request->validate([
            'group' => 'required|int|exists:groups,id',
        ]);

        $player->group_id = $request->group_id;
        $player->save();

        return response()->json([
            'player' => $player,
        ]);
    }

    public function playerSetLanguage(Request $request, Player $player) {
        $request->validate([
            'language' => 'required|string|exists:translations,lang',
        ]);

        $config = $player->configs->where('key', 'language')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'language';
        }

        $config->value = $request->language;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetCoins(Request $request, Player $player) {
        $request->validate([
            'coins' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'coins')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'coins';
        }

        $config->value = $request->coins;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerAddCoins(Request $request, Player $player) {
        $request->validate([
            'coins' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'coins')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'coins';
        }

        $config->value += $request->coins;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerRemoveCoins(Request $request, Player $player) {
        $request->validate([
            'coins' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'coins')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'coins';
        }

        $config->value -= $request->coins;
        if ($config->value < 0) {
            $config->value = 0;
        }
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetMoney(Request $request, Player $player) {
        $request->validate([
            'money' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'money')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'money';
        }

        $config->value = $request->money;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerAddMoney(Request $request, Player $player) {
        $request->validate([
            'money' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'money')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'money';
        }

        $config->value += $request->money;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerRemoveMoney(Request $request, Player $player) {
        $request->validate([
            'money' => 'required|integer|min:0',
        ]);

        $config = $player->configs->where('key', 'money')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'money';
        }

        $config->value -= $request->money;
        if ($config->value < 0) {
            $config->value = 0;
        }
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetHidePlayer(Request $request, Player $player) {
        $request->validate([
            'hide_player' => 'required|string|in:all,friends,none',
        ]);

        $config = $player->configs->where('key', 'hide_player')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'hide_player';
        }

        $config->value = $request->hide_player;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetConnectionNotification(Request $request, Player $player) {
        $request->validate([
            'connection_notification' => 'required|string|in:all,friends,none',
        ]);

        $config = $player->configs->where('key', 'connection_notification')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'connection_notification';
        }

        $config->value = $request->connection_notification;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetFriendInvitation(Request $request, Player $player) {
        $request->validate([
            'friend_invitation' => 'required|string|in:all,friends,none',
        ]);

        $config = $player->configs->where('key', 'friend_invitation')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'friend_invitation';
        }

        $config->value = $request->friend_invitation;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetFriendAcception(Request $request, Player $player) {
        $request->validate([
            'friend_acception' => 'required|string|in:accept_all,deny_all,none',
        ]);

        $config = $player->configs->where('key', 'friend_acception')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'friend_acception';
        }

        $config->value = $request->friend_acception;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerUpdateAllConfigs(Request $request, Player $player) {
        foreach ($request as $key => $value) {
            $config = $player->configs->where('key', $key)->first();
            if ($config === null) {
                $config = new PlayerConfig();
                $config->player_id = $player->id;
                $config->key = $key;
            }

            $config->value = $value;
            $config->save();
        }
        // $configs = $player->configs;
        // foreach ($request->configs as $key => $value) {
        //     $config = $configs->where('key', $key)->first();
        //     if ($config === null) {
        //         $config = new PlayerConfig();
        //         $config->player_id = $player->id;
        //         $config->key = $key;
        //     }

        //     $config->value = $value;
        //     $config->save();
        // }

        return response()->json([
            'configs' => $player->configs,
        ]);
    }

    public function playerSetPingNotification(Request $request, Player $player) {
        $request->validate([
            'ping_notification' => 'required|string|in:all,friends,none',
        ]);

        $config = $player->configs->where('key', 'ping_notification')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'ping_notification';
        }

        $config->value = $request->ping_notification;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function playerSetPrivateMessage(Request $request, Player $player) {
        $request->validate([
            'private_message' => 'required|string|in:all,friends,none',
        ]);

        $config = $player->configs->where('key', 'private_message')->first();
        if ($config === null) {
            $config = new PlayerConfig();
            $config->player_id = $player->id;
            $config->key = 'private_message';
        }

        $config->value = $request->private_message;
        $config->save();

        return response()->json([
            'config' => $config,
        ]);
    }

    public function maxPlayerCountByTime() {

        return view('chart');

    }

    public function maxPlayerCountByTimeValues(Request $request) {

        if (Cache::has("graph.values"))
            return Cache::get("graph.values");
        $step = 5;
        if (PlayerConnection::first() == null)
            return [];
        // $graphs = Graph::where('name', 'maxPlayerCountByTime')->get();

        $connections = PlayerConnection::all();
        $connections = $connections->filter(function($item) {
            return $item->disconnect_at != null;
        });
        $connections->each(function(&$item){
            $item->connect_at = Carbon::parse($item->connect_at, 'UTC');
            $item->disconnect_at = Carbon::parse($item->disconnect_at, 'UTC');
        });

        $min_date = $connections->first()->created_at;
        $date = $min_date;
        $endDate = Carbon::now();

        $labels = [];
        $data = ["quantity" => [], 'labels' => []];
        while ($date->isBefore($endDate)) {
            $next = $date->clone();
            $next = $next->addMinutes($step);
            $test = $connections->filter(function ($connection) use ($date, $next) {
                return $date->between($connection->connect_at, $connection->disconnect_at) && $next->between($connection->connect_at, $connection->disconnect_at);
            });
            $sv = [];
            $test->each(function($item) use (&$sv) {
                $sv[] = $item->id;
            });
            $data['labels'][] = $sv;
            $count = $test->count();
            $connections = $connections->filter(function($item) use ($next) {
                return $item->disconnect_at->isAfter($next);
            });
            $labels[] = $date->format('Y-m-d H:i:s');
            $data['quantity'][] = $count;

            $date = $date->addMinutes($step);
        }
        $result = [
            'labels' => $labels,
            'data' => $data,
        ];
        Cache::add("graph.values", $result, now()->addHours(1));

        return $result;
    }
}
