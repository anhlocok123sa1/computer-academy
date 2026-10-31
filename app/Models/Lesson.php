<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'type',
        'video_url',
        'image_url',
        'attachment_url',
        'content',
        'exercises',
        'duration',
        'is_free_preview',
        'sort_order',
    ];

    protected $casts = [
        'exercises' => 'array',
        'is_free_preview' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
