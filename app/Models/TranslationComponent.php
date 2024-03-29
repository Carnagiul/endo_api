<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_id',
        'order',
        'parent_id',
        'prev_id',
        'text',
        'color',
        'clickEventType',
        'clickEventValue',
        'hoverEventType',
        'hoverEventContents',
        'bold',
        'italic',
        'strikethrough',
        'underlined',
        'obfuscated',
        'team_color'
    ];

    public function translation() {
        return $this->belongsTo(Translation::class);
    }

    public function parent() {
        return $this->belongsTo(TranslationComponent::class);
    }

    public function children() {
        return $this->hasMany(TranslationComponent::class);
    }

    public function prev() {
        return $this->belongsTo(Translation::class, 'prev_id');
    }
}
