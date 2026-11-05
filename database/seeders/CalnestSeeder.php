<?php

namespace Database\Seeders;

use App\Models\CalnestManualBirthday;
use App\Models\CalnestPreference;
use App\Models\CalnestReminderSetting;
use App\Models\CalnestSelectedCalendar;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use App\Models\GoogleContact;
use App\Models\GoogleTask;
use App\Models\GoogleTaskList;
use App\Models\MicrosoftAccount;
use App\Models\MicrosoftCalendar;
use App\Models\MicrosoftCalendarEvent;
use App\Models\MicrosoftTodoList;
use App\Models\MicrosoftTodoTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CalnestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Demo User for CalNest Backend
        $user = User::firstOrCreate(
            ['email' => 'calnest@example.com'],
            [
                'name' => 'CalNest Demo User',
                'password' => Hash::make('password123'),
            ]
        );

        // 1. Google Calendar
        GoogleCalendar::updateOrCreate(
            ['id' => 'user@gmail.com'],
            [
                'summary' => 'user@gmail.com',
                'primary' => true,
                'accessRole' => 'owner',
                'backgroundColor' => '#00A4EF',
                'colorId' => 'lightBlue',
            ]
        );

        // Google Events
        GoogleCalendarEvent::updateOrCreate(
            ['id' => '7k3c9qa1b2'],
            [
                'calendar_id' => 'user@gmail.com',
                'status' => 'confirmed',
                'summary' => 'Team standup',
                'description' => 'Daily sync',
                'location' => 'Room 4',
                'start_dateTime' => '2026-10-17T10:00:00+05:30',
                'end_dateTime' => '2026-10-17T10:30:00+05:30',
                'colorId' => '5',
            ]
        );

        GoogleCalendarEvent::updateOrCreate(
            ['id' => 'birthday-2026'],
            [
                'calendar_id' => 'user@gmail.com',
                'status' => 'confirmed',
                'summary' => "Priya's birthday",
                'start_date' => '2026-10-18',
                'end_date' => '2026-10-19',
            ]
        );

        GoogleCalendarEvent::updateOrCreate(
            ['id' => '4hn2m8e0pq'],
            [
                'calendar_id' => 'user@gmail.com',
                'status' => 'confirmed',
                'htmlLink' => 'https://www.google.com/calendar/event?eid=sample',
                'summary' => 'Client call',
                'start_dateTime' => '2026-10-20T15:00:00+05:30',
                'end_dateTime' => '2026-10-20T16:00:00+05:30',
            ]
        );

        // 2. Google Task Lists & Tasks
        GoogleTaskList::updateOrCreate(
            ['id' => 'MTIzNDU2'],
            [
                'title' => 'My Tasks',
                'updated_at_iso' => '2026-10-16T09:12:00.000Z',
            ]
        );

        GoogleTask::updateOrCreate(
            ['id' => 'dGFzay0x'],
            [
                'task_list_id' => 'MTIzNDU2',
                'title' => 'Send invoice',
                'notes' => 'March batch',
                'status' => 'needsAction',
                'due' => '2026-10-18T00:00:00.000Z',
            ]
        );

        GoogleTask::updateOrCreate(
            ['id' => 'dGFzay0y'],
            [
                'task_list_id' => 'MTIzNDU2',
                'title' => 'Renew domain',
                'notes' => 'btotmail.com',
                'status' => 'needsAction',
                'due' => '2026-10-25T00:00:00.000Z',
            ]
        );

        // 3. Google Contacts (People API)
        GoogleContact::updateOrCreate(
            ['resourceName' => 'people/c1234567890'],
            [
                'displayName' => 'Priya Shah',
                'givenName' => 'Priya',
                'familyName' => 'Shah',
                'birthday_year' => 1994,
                'birthday_month' => 10,
                'birthday_day' => 18,
                'phone_value' => '+91 98765 43210',
                'phone_type' => 'mobile',
                'photo_url' => 'https://lh3.googleusercontent.com/sample',
            ]
        );

        // 4. Microsoft Account
        MicrosoftAccount::updateOrCreate(
            ['id' => 'a1b2c3d4'],
            [
                'displayName' => 'Ravi Patel',
                'mail' => 'ravi@outlook.com',
                'userPrincipalName' => 'ravi@outlook.com',
            ]
        );

        // 5. Microsoft Calendar & Events
        MicrosoftCalendar::updateOrCreate(
            ['id' => 'AAMkAGI2'],
            [
                'name' => 'Calendar',
                'isDefaultCalendar' => true,
                'color' => 'auto',
            ]
        );

        MicrosoftCalendarEvent::updateOrCreate(
            ['id' => 'AAMkAGI2-evt1'],
            [
                'calendar_id' => 'AAMkAGI2',
                'subject' => 'Dentist',
                'isAllDay' => false,
                'start_dateTime' => '2026-10-17T11:00:00.0000000',
                'start_timeZone' => 'UTC',
                'end_dateTime' => '2026-10-17T11:30:00.0000000',
                'end_timeZone' => 'UTC',
                'location_displayName' => 'City Clinic',
                'bodyPreview' => 'Check-up',
            ]
        );

        MicrosoftCalendarEvent::updateOrCreate(
            ['id' => 'AAMkAGI2-evt2'],
            [
                'calendar_id' => 'AAMkAGI2',
                'subject' => 'Project review',
                'isAllDay' => false,
                'start_dateTime' => '2026-10-19T08:00:00.0000000',
                'start_timeZone' => 'UTC',
                'end_dateTime' => '2026-10-19T09:00:00.0000000',
                'end_timeZone' => 'UTC',
            ]
        );

        MicrosoftCalendarEvent::updateOrCreate(
            ['id' => 'AAMkAGI2-evt3'],
            [
                'calendar_id' => 'AAMkAGI2',
                'subject' => 'Client call',
                'isAllDay' => false,
                'start_dateTime' => '2026-10-20T10:00:00',
                'start_timeZone' => 'UTC',
                'end_dateTime' => '2026-10-20T11:00:00',
                'end_timeZone' => 'UTC',
                'location_displayName' => 'Zoom',
                'body_contentType' => 'text',
                'body_content' => 'Discuss renewal',
            ]
        );

        // 6. Microsoft To Do Lists & Tasks
        MicrosoftTodoList::updateOrCreate(
            ['id' => 'AQMkADAw'],
            [
                'displayName' => 'Tasks',
                'isOwner' => true,
                'wellknownListName' => 'defaultList',
            ]
        );

        MicrosoftTodoTask::updateOrCreate(
            ['id' => 'AQMkADAw-t1'],
            [
                'list_id' => 'AQMkADAw',
                'title' => 'Pay rent',
                'status' => 'notStarted',
                'body_contentType' => 'text',
                'body_content' => '',
                'due_dateTime' => '2026-10-18T00:00:00.0000000',
                'due_timeZone' => 'UTC',
            ]
        );

        MicrosoftTodoTask::updateOrCreate(
            ['id' => 'AQMkADAw-t2'],
            [
                'list_id' => 'AQMkADAw',
                'title' => 'Renew domain',
                'status' => 'notStarted',
                'body_contentType' => 'text',
                'body_content' => 'btotmail.com',
                'due_dateTime' => '2026-10-25T00:00:00',
                'due_timeZone' => 'UTC',
            ]
        );

        // 7. CalNest Local Storage Sync
        CalnestPreference::updateOrCreate(
            ['key' => 'app_theme'],
            ['value' => 'system']
        );
        CalnestPreference::updateOrCreate(
            ['key' => 'calendar_view_mode'],
            ['value' => 'month']
        );

        CalnestManualBirthday::updateOrCreate(
            ['name' => 'Priya Shah'],
            [
                'date' => '1994-10-18',
                'phone' => '+91 98765 43210',
                'relationship' => 'Friend',
                'notes' => 'Likes chocolate cake',
            ]
        );

        CalnestSelectedCalendar::updateOrCreate(
            ['calendar_id' => 'user@gmail.com', 'provider' => 'google'],
            ['is_selected' => true]
        );

        CalnestSelectedCalendar::updateOrCreate(
            ['calendar_id' => 'AAMkAGI2', 'provider' => 'microsoft'],
            ['is_selected' => true]
        );

        CalnestReminderSetting::updateOrCreate(
            ['setting_key' => 'birthday_reminder_time'],
            ['setting_value' => '09:00']
        );

        CalnestReminderSetting::updateOrCreate(
            ['setting_key' => 'notifications_enabled'],
            ['setting_value' => 'true']
        );
    }
}
