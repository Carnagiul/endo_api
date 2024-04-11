<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'priority',
        'default',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function configs()
    {
        return $this->hasMany(GroupConfig::class);
    }
}
