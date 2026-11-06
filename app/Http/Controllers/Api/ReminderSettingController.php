<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestReminderSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderSettingController extends Controller
{
    /**
     * Helper to cast setting values to expected primitive types
     */
    protected function formatSettings($settings): array
    {
        $formatted = [];
        foreach ($settings as $key => $value) {
            if ($key === 'event_reminder_minutes' && is_numeric($value)) {
                $formatted[$key] = (int) $value;
            } elseif ($key === 'birthday_reminder_enabled') {
                $formatted[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $formatted[$key] = $value;
            }
        }
        return $formatted;
    }

    /**
     * Get reminder settings (GET /api/calnest/reminder-settings)
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $query = CalnestReminderSetting::query();
        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        $raw = $query->pluck('setting_value', 'setting_key');
        return response()->json($this->formatSettings($raw));
    }

    /**
     * Save reminder settings (POST /api/calnest/reminder-settings)
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
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

        $query = CalnestReminderSetting::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $raw = $query->pluck('setting_value', 'setting_key');
        return response()->json($this->formatSettings($raw), 200);
    }
}
