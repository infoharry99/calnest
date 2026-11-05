<?php

namespace Tests\Feature;

use Database\Seeders\CalnestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalnestApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CalnestSeeder::class);
    }

    public function test_welcome_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('CalNest API Reference');
    }

    public function test_calnest_overview(): void
    {
        $response = $this->getJson('/api/calnest/overview');
        $response->assertStatus(200)
            ->assertJsonPath('status', 'healthy')
            ->assertJsonPath('counts.google_events', 3);
    }

    public function test_google_oauth_token_exchange(): void
    {
        $response = $this->postJson('/api/oauth/google/token', [
            'email' => 'user@gmail.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'scope', 'user']);
    }

    public function test_microsoft_oauth_token_exchange(): void
    {
        $response = $this->postJson('/consumers/oauth2/v2.0/token', [
            'grant_type' => 'authorization_code',
            'code' => 'sample_code',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'refresh_token']);
    }

    public function test_google_calendar_list(): void
    {
        $response = $this->getJson('/calendar/v3/users/me/calendarList');
        $response->assertStatus(200)
            ->assertJsonPath('kind', 'calendar#calendarList')
            ->assertJsonFragment(['id' => 'user@gmail.com']);
    }

    public function test_google_calendar_events_list_and_crud(): void
    {
        // 1. List events
        $listRes = $this->getJson('/calendar/v3/calendars/user@gmail.com/events');
        $listRes->assertStatus(200)
            ->assertJsonPath('kind', 'calendar#events')
            ->assertJsonFragment(['summary' => 'Team standup']);

        // 2. Create event
        $createRes = $this->postJson('/calendar/v3/calendars/user@gmail.com/events', [
            'summary' => 'New Client Meeting',
            'description' => 'Contract review',
            'location' => 'Google Meet',
            'start' => ['dateTime' => '2026-10-21T10:00:00+05:30'],
            'end' => ['dateTime' => '2026-10-21T11:00:00+05:30'],
        ]);
        $createRes->assertStatus(200)
            ->assertJsonFragment(['summary' => 'New Client Meeting']);

        $newEventId = $createRes->json('id');

        // 3. Update event
        $patchRes = $this->patchJson("/calendar/v3/calendars/user@gmail.com/events/{$newEventId}", [
            'summary' => 'New Client Meeting (Confirmed)',
        ]);
        $patchRes->assertStatus(200)
            ->assertJsonFragment(['summary' => 'New Client Meeting (Confirmed)']);

        // 4. Delete event
        $delRes = $this->deleteJson("/calendar/v3/calendars/user@gmail.com/events/{$newEventId}");
        $delRes->assertStatus(204);
    }

    public function test_google_tasks_crud(): void
    {
        // 1. List task lists
        $listRes = $this->getJson('/tasks/v1/users/@me/lists');
        $listRes->assertStatus(200)
            ->assertJsonPath('kind', 'tasks#taskLists')
            ->assertJsonFragment(['title' => 'My Tasks']);

        // 2. List tasks in list
        $tasksRes = $this->getJson('/tasks/v1/lists/MTIzNDU2/tasks');
        $tasksRes->assertStatus(200)
            ->assertJsonPath('kind', 'tasks#tasks')
            ->assertJsonFragment(['title' => 'Send invoice']);

        // 3. Create task
        $createRes = $this->postJson('/tasks/v1/lists/MTIzNDU2/tasks', [
            'title' => 'Prepare tax documents',
            'notes' => 'Q4 taxes',
            'status' => 'needsAction',
        ]);
        $createRes->assertStatus(200)
            ->assertJsonFragment(['title' => 'Prepare tax documents']);

        $taskId = $createRes->json('id');

        // 4. Edit task
        $patchRes = $this->patchJson("/tasks/v1/lists/MTIzNDU2/tasks/{$taskId}", [
            'status' => 'completed',
        ]);
        $patchRes->assertStatus(200)
            ->assertJsonPath('status', 'completed');

        // 5. Delete task
        $delRes = $this->deleteJson("/tasks/v1/lists/MTIzNDU2/tasks/{$taskId}");
        $delRes->assertStatus(204);
    }

    public function test_google_people_connections(): void
    {
        $response = $this->getJson('/people/v1/people/me/connections');
        $response->assertStatus(200)
            ->assertJsonStructure(['connections', 'totalPeople'])
            ->assertJsonFragment(['displayName' => 'Priya Shah']);
    }

    public function test_microsoft_graph_profile_and_calendar(): void
    {
        // Profile
        $meRes = $this->getJson('/v1.0/me');
        $meRes->assertStatus(200)
            ->assertJsonPath('displayName', 'Ravi Patel');

        // Calendars
        $calsRes = $this->getJson('/v1.0/me/calendars');
        $calsRes->assertStatus(200)
            ->assertJsonFragment(['name' => 'Calendar']);

        // Primary CalendarView
        $viewRes = $this->getJson('/v1.0/me/calendarView');
        $viewRes->assertStatus(200)
            ->assertJsonFragment(['subject' => 'Dentist']);

        // Specific CalendarView
        $specRes = $this->getJson('/v1.0/me/calendars/AAMkAGI2/calendarView');
        $specRes->assertStatus(200);

        // Create Outlook Event
        $createRes = $this->postJson('/v1.0/me/calendars/AAMkAGI2/events', [
            'subject' => 'Quarterly Review',
            'start' => ['dateTime' => '2026-10-22T09:00:00', 'timeZone' => 'UTC'],
            'end' => ['dateTime' => '2026-10-22T10:00:00', 'timeZone' => 'UTC'],
        ]);
        $createRes->assertStatus(201)
            ->assertJsonPath('subject', 'Quarterly Review');

        $eventId = $createRes->json('id');

        // Patch Outlook Event
        $patchRes = $this->patchJson("/v1.0/me/calendars/AAMkAGI2/events/{$eventId}", [
            'subject' => 'Quarterly Review (Updated)',
        ]);
        $patchRes->assertStatus(200)
            ->assertJsonPath('subject', 'Quarterly Review (Updated)');

        // Delete Outlook Event
        $delRes = $this->deleteJson("/v1.0/me/calendars/AAMkAGI2/events/{$eventId}");
        $delRes->assertStatus(204);
    }

    public function test_microsoft_todo_crud(): void
    {
        // List To Do lists
        $listRes = $this->getJson('/v1.0/me/todo/lists');
        $listRes->assertStatus(200)
            ->assertJsonFragment(['displayName' => 'Tasks']);

        // List tasks
        $tasksRes = $this->getJson('/v1.0/me/todo/lists/AQMkADAw/tasks');
        $tasksRes->assertStatus(200)
            ->assertJsonFragment(['title' => 'Pay rent']);

        // Create task
        $createRes = $this->postJson('/v1.0/me/todo/lists/AQMkADAw/tasks', [
            'title' => 'Submit report',
            'status' => 'notStarted',
        ]);
        $createRes->assertStatus(201)
            ->assertJsonPath('title', 'Submit report');

        $taskId = $createRes->json('id');

        // Update task
        $patchRes = $this->patchJson("/v1.0/me/todo/lists/AQMkADAw/tasks/{$taskId}", [
            'status' => 'completed',
        ]);
        $patchRes->assertStatus(200)
            ->assertJsonPath('status', 'completed');

        // Delete task
        $delRes = $this->deleteJson("/v1.0/me/todo/lists/AQMkADAw/tasks/{$taskId}");
        $delRes->assertStatus(204);
    }

    public function test_device_links_generation(): void
    {
        // WhatsApp
        $waRes = $this->getJson('/api/device-links/whatsapp?phone=' . urlencode('+91 98765 43210') . '&message=Happy%20Birthday');
        $waRes->assertStatus(200)
            ->assertJsonFragment(['url' => 'https://wa.me/919876543210?text=Happy%20Birthday']);

        // SMS
        $smsRes = $this->getJson('/api/device-links/sms?phone=' . urlencode('+91 98765 43210') . '&message=Happy%20Birthday');
        $smsRes->assertStatus(200)
            ->assertJsonPath('phone', '+91 98765 43210');
    }

    public function test_calnest_backend_preferences_and_birthdays(): void
    {
        // Preferences
        $prefRes = $this->postJson('/api/calnest/preferences', [
            'dark_mode' => true,
            'default_calendar' => 'user@gmail.com',
        ]);
        $prefRes->assertStatus(200);

        $getPref = $this->getJson('/api/calnest/preferences');
        $getPref->assertStatus(200)
            ->assertJsonFragment(['dark_mode' => true]);

        // Manual Birthday
        $bdayRes = $this->postJson('/api/calnest/manual-birthdays', [
            'name' => 'Alex Johnson',
            'date' => '1995-12-05',
            'phone' => '+1 555 123 4567',
        ]);
        $bdayRes->assertStatus(201)
            ->assertJsonPath('name', 'Alex Johnson');
    }
}
