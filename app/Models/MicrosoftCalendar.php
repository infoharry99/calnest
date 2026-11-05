<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicrosoftCalendar extends Model
{
    use HasFactory;

    protected $table = 'microsoft_calendars';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'isDefaultCalendar',
        'color',
    ];

    protected $casts = [
        'isDefaultCalendar' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(MicrosoftCalendarEvent::class, 'calendar_id', 'id');
    }

    public function toGraphArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isDefaultCalendar' => (bool)$this->isDefaultCalendar,
            'color' => $this->color ?? 'auto',
        ];
    }
}
