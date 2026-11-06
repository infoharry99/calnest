<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestEvent;
use App\Models\CalnestGreeting;
use App\Models\CalnestManualBirthday;
use App\Models\CalnestPreference;
use App\Models\CalnestReminderSetting;
use App\Models\CalnestSelectedCalendar;
use App\Models\CalnestTask;
use App\Models\GoogleCalendarEvent;
use App\Models\GoogleContact;
use App\Models\GoogleTask;
use App\Models\MicrosoftCalendarEvent;
use App\Models\MicrosoftTodoTask;
use Illuminate\Http\JsonResponse;

class OverviewController extends Controller
{
    /**
     * CalNest Overview / Health check (GET /api/calnest/overview)
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'app' => 'CalNest Backend API',
            'version' => '1.0.0',
            'counts' => [
                'greetings' => CalnestGreeting::count(),
                'events' => CalnestEvent::count(),
                'tasks' => CalnestTask::count(),
                'manual_birthdays' => CalnestManualBirthday::count(),
                'google_events' => GoogleCalendarEvent::count(),
                'google_tasks' => GoogleTask::count(),
                'google_contacts' => GoogleContact::count(),
                'microsoft_events' => MicrosoftCalendarEvent::count(),
                'microsoft_tasks' => MicrosoftTodoTask::count(),
                'selected_calendars' => CalnestSelectedCalendar::count(),
            ],
            'preferences' => CalnestPreference::pluck('value', 'key'),
            'reminder_settings' => CalnestReminderSetting::pluck('setting_value', 'setting_key'),
        ]);
    }
}
