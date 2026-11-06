<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestReminderSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderSettingController extends Controller
{
    /**
     * Helper to return standard, typed reminder settings object:
     * {
     *   "event_reminder_minutes": 15,
     *   "birthday_reminder_enabled": true,
     *   "birthday_reminder_time": "09:00"
     * }
     */
    protected function formatSettings(array $settings): array
    {
        // Handle legacy alias if present
        $birthdayEnabled = true;
        if (array_key_exists('birthday_reminder_enabled', $settings)) {
            $birthdayEnabled = filter_var($settings['birthday_reminder_enabled'], FILTER_VALIDATE_BOOLEAN);
        } elseif (array_key_exists('notifications_enabled', $settings)) {
            $birthdayEnabled = filter_var($settings['notifications_enabled'], FILTER_VALIDATE_BOOLEAN);
        }

        $eventMinutes = 15;
        if (array_key_exists('event_reminder_minutes', $settings) && is_numeric($settings['event_reminder_minutes'])) {
            $eventMinutes = (int) $settings['event_reminder_minutes'];
        }

        $birthdayTime = $settings['birthday_reminder_time'] ?? '09:00';

        return [
            'event_reminder_minutes' => $eventMinutes,
            'birthday_reminder_enabled' => $birthdayEnabled,
            'birthday_reminder_time' => (string) $birthdayTime,
        ];
    }

    /**
     * Get reminder settings (GET /api/calnest/reminder-settings)
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $raw = CalnestReminderSetting::where('user_id', $userId)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        return response()->json($this->formatSettings($raw), 200);
    }

    /**
     * Save reminder settings (POST /api/calnest/reminder-settings)
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->all();

        foreach ($data as $key => $value) {
            $stringValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

            CalnestReminderSetting::updateOrCreate(
                [
                    'user_id' => $userId,
                    'setting_key' => $key,
                ],
                [
                    'setting_value' => $stringValue,
                ]
            );
        }

        $raw = CalnestReminderSetting::where('user_id', $userId)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        return response()->json($this->formatSettings($raw), 200);
    }
}
