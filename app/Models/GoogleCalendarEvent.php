<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarEvent extends Model
{
    use HasFactory;

    protected $table = 'google_calendar_events';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'calendar_id',
        'status',
        'summary',
        'description',
        'location',
        'htmlLink',
        'start_dateTime',
        'start_date',
        'end_dateTime',
        'end_date',
        'colorId',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(GoogleCalendar::class, 'calendar_id', 'id');
    }

    public function toGoogleArray(): array
    {
        $start = [];
        if ($this->start_dateTime) {
            $start['dateTime'] = $this->start_dateTime;
        } elseif ($this->start_date) {
            $start['date'] = $this->start_date;
        }

        $end = [];
        if ($this->end_dateTime) {
            $end['dateTime'] = $this->end_dateTime;
        } elseif ($this->end_date) {
            $end['date'] = $this->end_date;
        }

        $data = [
            'id' => $this->id,
            'status' => $this->status ?? 'confirmed',
            'summary' => $this->summary,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->location !== null) {
            $data['location'] = $this->location;
        }
        if ($this->htmlLink !== null) {
            $data['htmlLink'] = $this->htmlLink;
        }
        $data['start'] = $start;
        $data['end'] = $end;

        if ($this->colorId !== null) {
            $data['colorId'] = $this->colorId;
        }

        return $data;
    }
}
