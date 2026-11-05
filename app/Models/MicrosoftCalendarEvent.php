<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrosoftCalendarEvent extends Model
{
    use HasFactory;

    protected $table = 'microsoft_calendar_events';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'calendar_id',
        'subject',
        'isAllDay',
        'start_dateTime',
        'start_timeZone',
        'end_dateTime',
        'end_timeZone',
        'location_displayName',
        'bodyPreview',
        'body_content',
        'body_contentType',
    ];

    protected $casts = [
        'isAllDay' => 'boolean',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(MicrosoftCalendar::class, 'calendar_id', 'id');
    }

    public function toGraphArray(?array $select = null): array
    {
        $all = [
            'id' => $this->id,
            'subject' => $this->subject,
            'isAllDay' => (bool)$this->isAllDay,
            'start' => [
                'dateTime' => $this->start_dateTime,
                'timeZone' => $this->start_timeZone ?? 'UTC',
            ],
            'end' => [
                'dateTime' => $this->end_dateTime,
                'timeZone' => $this->end_timeZone ?? 'UTC',
            ],
            'location' => [
                'displayName' => $this->location_displayName ?? '',
            ],
            'bodyPreview' => $this->bodyPreview ?? '',
        ];

        if ($select && count($select) > 0) {
            $filtered = [];
            foreach ($select as $field) {
                $field = trim($field);
                if (array_key_exists($field, $all)) {
                    $filtered[$field] = $all[$field];
                }
            }
            return !empty($filtered) ? $filtered : $all;
        }

        return $all;
    }
}
