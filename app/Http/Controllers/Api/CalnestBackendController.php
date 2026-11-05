<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestManualBirthday;
use App\Models\CalnestPreference;
use App\Models\CalnestReminderSetting;
use App\Models\CalnestSelectedCalendar;
use App\Models\GoogleCalendarEvent;
use App\Models\GoogleContact;
use App\Models\GoogleTask;
use App\Models\MicrosoftCalendarEvent;
use App\Models\MicrosoftTodoTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalnestBackendController extends Controller
{
    /**
     * CalNest Overview / Dashboard sync status
     */
    public function overview(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'app' => 'CalNest Backend API',
            'version' => '1.0.0',
            'counts' => [
                'google_events' => GoogleCalendarEvent::count(),
                'google_tasks' => GoogleTask::count(),
                'google_contacts' => GoogleContact::count(),
                'microsoft_events' => MicrosoftCalendarEvent::count(),
                'microsoft_tasks' => MicrosoftTodoTask::count(),
                'manual_birthdays' => CalnestManualBirthday::count(),
                'selected_calendars' => CalnestSelectedCalendar::count(),
            ],
            'reminder_settings' => CalnestReminderSetting::pluck('setting_value', 'setting_key'),
            'preferences' => CalnestPreference::pluck('value', 'key'),
        ]);
    }

    /**
     * Get or set user preferences (SharedPreferences sync).
     */
    public function getPreferences(): JsonResponse
    {
        $prefs = CalnestPreference::all()->pluck('value', 'key');
        return response()->json($prefs);
    }

    public function savePreferences(Request $request): JsonResponse
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            CalnestPreference::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'message' => 'Preferences saved successfully',
            'preferences' => CalnestPreference::all()->pluck('value', 'key'),
        ]);
    }

    /**
     * Manual birthdays (saved locally on device in SharedPreferences).
     */
    public function listManualBirthdays(): JsonResponse
    {
        $birthdays = CalnestManualBirthday::all();
        return response()->json([
            'items' => $birthdays,
            'total' => $birthdays->count(),
        ]);
    }

    public function createManualBirthday(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|string', // YYYY-MM-DD or MM-DD
            'phone' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $birthday = CalnestManualBirthday::create($validated);

        return response()->json($birthday, 201);
    }

    public function deleteManualBirthday(int $id): Response
    {
        $birthday = CalnestManualBirthday::find($id);
        if ($birthday) {
            $birthday->delete();
        }

        return response()->noContent();
    }

    /**
     * Selected calendars sync
     */
    public function listSelectedCalendars(): JsonResponse
    {
        $calendars = CalnestSelectedCalendar::all();
        return response()->json([
            'items' => $calendars,
        ]);
    }

    public function saveSelectedCalendars(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'calendars' => 'required|array',
            'calendars.*.calendar_id' => 'required|string',
            'calendars.*.provider' => 'required|string|in:google,microsoft',
            'calendars.*.is_selected' => 'required|boolean',
        ]);

        foreach ($validated['calendars'] as $cal) {
            CalnestSelectedCalendar::updateOrCreate(
                [
                    'calendar_id' => $cal['calendar_id'],
                    'provider' => $cal['provider'],
                ],
                [
                    'is_selected' => $cal['is_selected'],
                ]
            );
        }

        return response()->json([
            'message' => 'Selected calendars updated successfully',
            'items' => CalnestSelectedCalendar::all(),
        ]);
    }

    /**
     * Reminder settings sync
     */
    public function getReminderSettings(): JsonResponse
    {
        $settings = CalnestReminderSetting::pluck('setting_value', 'setting_key');
        return response()->json($settings);
    }

    public function saveReminderSettings(Request $request): JsonResponse
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            CalnestReminderSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => (string)$value]
            );
        }

        return response()->json([
            'message' => 'Reminder settings saved successfully',
            'settings' => CalnestReminderSetting::pluck('setting_value', 'setting_key'),
        ]);
    }
}
