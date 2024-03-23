<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerConnection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'player_id',
        'player_ip_id',
        'connect_at',
        'disconnect_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function ip()
    {
        return $this->belongsTo(PlayerIp::class, 'player_ip_id');
    }
}
