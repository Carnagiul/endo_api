<?php

namespace App\Observers;

use App\Models\Player;
use App\Models\PlayerConfig;

class PlayerObserver
{
    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        //
        $hidePlayerConfig = new PlayerConfig();
        $hidePlayerConfig->player_id = $player->id;
        $hidePlayerConfig->key = 'hide_player';
        $hidePlayerConfig->value = 'none';
        $hidePlayerConfig->save();

        $connectionNotificationConfig = new PlayerConfig();
        $connectionNotificationConfig->player_id = $player->id;
        $connectionNotificationConfig->key = 'connection_notification';
        $connectionNotificationConfig->value = 'all';
        $connectionNotificationConfig->save();

        $friendInvitationConfig = new PlayerConfig();
        $friendInvitationConfig->player_id = $player->id;
        $friendInvitationConfig->key = 'friend_invitation';
        $friendInvitationConfig->value = 'all';
        $friendInvitationConfig->save();

        $friendAcceptation = new PlayerConfig();
        $friendAcceptation->player_id = $player->id;
        $friendAcceptation->key = 'friend_acception';
        $friendAcceptation->value = 'await';
        $friendAcceptation->save();

        $pingNotification = new PlayerConfig();
        $pingNotification->player_id = $player->id;
        $pingNotification->key = 'ping_notification';
        $pingNotification->value = 'all';
        $pingNotification->save();

        $privateMessage = new PlayerConfig();
        $privateMessage->player_id = $player->id;
        $privateMessage->key = 'private_message';
        $privateMessage->value = 'all';
        $privateMessage->save();


    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "deleted" event.
     */
    public function deleted(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "restored" event.
     */
    public function restored(Player $player): void
    {
        //
    }

    /**
     * Handle the Player "force deleted" event.
     */
    public function forceDeleted(Player $player): void
    {
        //
    }
}
