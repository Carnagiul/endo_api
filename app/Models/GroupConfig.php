<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupConfig extends Model
{
    use HasFactory;

    protected $table = 'group_configs';

    protected $fillable = [
        'group_id',
        'key',
        'value',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
