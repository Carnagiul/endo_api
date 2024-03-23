<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerMute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'player_id',
        'reason',
        'punisher_id',
        'deleted_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function punisher()
    {
        return $this->belongsTo(Player::class);
    }
}
