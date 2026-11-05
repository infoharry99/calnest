<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalnestReminderSetting extends Model
{
    use HasFactory;

    protected $table = 'calnest_reminder_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];
}
