<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Greeting extends Model
{
    use HasFactory;

    protected $table = 'greetings';

    protected $fillable = [
        'title',
        'message',
        'category',
        'emoji',
        'language',
        'author',
        'image_url',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function toResponseArray(): array
    {
        $tags = $this->tags;
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            $tags = is_array($decoded) ? $decoded : array_map('trim', explode(',', $tags));
        }

        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'message' => (string) $this->message,
            'category' => (string) $this->category,
            'emoji' => $this->emoji,
            'language' => (string) ($this->language ?: 'English'),
            'author' => (string) ($this->author ?: 'CalNest'),
            'image_url' => $this->image_url,
            'tags' => is_array($tags) ? $tags : [],
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
