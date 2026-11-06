<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalnestEvent extends Model
{
    use HasFactory;

    protected $table = 'calnest_events';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'start',
        'end',
        'is_all_day',
        'color',
    ];

    protected $casts = [
        'is_all_day' => 'boolean',
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
            'description' => $this->description,
            'location' => $this->location,
            'start' => (string) $this->start,
            'end' => (string) $this->end,
            'is_all_day' => (bool) $this->is_all_day,
            'color' => $this->color ?? '#3B82F6',
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
