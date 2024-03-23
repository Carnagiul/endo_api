<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'uuid',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'email',
    ];

    public function bans()
    {
        return $this->hasMany(PlayerPunishment::class, 'player_id');
    }

    public function banGiven()
    {
        return $this->hasMany(PlayerPunishment::class, 'punisher_id');
    }

    public function ips() {
        return $this->hasMany(PlayerIp::class);
    }

    public function logs() {
        return $this->hasMany(PlayerConnection::class);
    }

    public function mutes()
    {
        return $this->hasMany(PlayerMute::class, 'player_id');
    }

    public function mutesGiven()
    {
        return $this->hasMany(PlayerMute::class, 'punisher_id');
    }

    public function freezes()
    {
        return $this->hasMany(PlayerFreeze::class, 'player_id');
    }

    public function freezesGiven()
    {
        return $this->hasMany(PlayerFreeze::class, 'punisher_id');
    }

    public function configs() {
        return $this->hasMany(PlayerConfig::class);
    }
}
