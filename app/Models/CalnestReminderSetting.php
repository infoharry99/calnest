<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalnestReminderSetting extends Model
{
    use HasFactory;

    protected $table = 'calnest_reminder_settings';

    protected $fillable = [
        'user_id',
        'setting_key',
        'setting_value',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
