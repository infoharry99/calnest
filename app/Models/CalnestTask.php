<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalnestTask extends Model
{
    use HasFactory;

    protected $table = 'calnest_tasks';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'due',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toResponseArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'description' => $this->description ?? '',
            'priority' => (string) ($this->priority ?: 'medium'),
            'due' => $this->due,
            'completed' => (bool) $this->completed,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
