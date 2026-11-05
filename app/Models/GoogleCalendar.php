<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleCalendar extends Model
{
    use HasFactory;

    protected $table = 'google_calendars';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'summary',
        'primary',
        'accessRole',
        'backgroundColor',
        'colorId',
    ];

    protected $casts = [
        'primary' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(GoogleCalendarEvent::class, 'calendar_id', 'id');
    }

    public function toGoogleArray(): array
    {
        return [
            'id' => $this->id,
            'summary' => $this->summary,
            'primary' => (bool)$this->primary,
            'accessRole' => $this->accessRole ?? 'owner',
            'backgroundColor' => $this->backgroundColor,
            'colorId' => $this->colorId,
        ];
    }
}
