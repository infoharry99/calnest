<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalnestSelectedCalendar extends Model
{
    use HasFactory;

    protected $table = 'calnest_selected_calendars';

    protected $fillable = [
        'calendar_id',
        'provider',
        'is_selected',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    ];
}
