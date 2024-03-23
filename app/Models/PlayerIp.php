<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerIp extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'player_id',
        'ip',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function logs()
    {
        return $this->hasMany(PlayerConnection::class, 'player_ip_id');
    }
}
