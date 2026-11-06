<?php

namespace Tests\Feature;

use App\Models\CalnestEvent;
use App\Models\CalnestManualBirthday;
use App\Models\CalnestTask;
use App\Models\CalnestVerificationCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CalnestAppRequirementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_registration_and_login_flow(): void
    {
        // 1. Register
        $registerRes = $this->postJson('/api/calnest/register', [
            'name' => 'Ganesh Patel',
            'email' => 'ganesh@example.com',
            'password' => 'secret123',
        ]);

        $registerRes->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email', 'photo_url', 'token'])
            ->assertJson([
                'name' => 'Ganesh Patel',
                'email' => 'ganesh@example.com',
                'photo_url' => null,
            ]);

        $token = $registerRes->json('token');
        $this->assertNotEmpty($token);

        // 2. Me endpoint with token
        $meRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/calnest/me');
        $meRes->assertStatus(200)
            ->assertJson([
                'name' => 'Ganesh Patel',
                'email' => 'ganesh@example.com',
            ]);

        // 3. Me endpoint without token fails 401
        $this->flushHeaders();
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/calnest/me')->assertStatus(401);

        // 4. Login with invalid password
        $this->postJson('/api/calnest/login', [
            'email' => 'ganesh@example.com',
            'password' => 'wrongpass',
        ])->assertStatus(401)
          ->assertJson(['message' => 'Invalid email or password']);

        // 5. Login with correct password
        $loginRes = $this->postJson('/api/calnest/login', [
            'email' => 'ganesh@example.com',
            'password' => 'secret123',
        ]);
        $loginRes->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'photo_url', 'token']);

        // 6. Logout
        $logoutRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/calnest/logout');
        $logoutRes->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_events_crud_and_date_range_filtering(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Create Event
        $createRes = $this->withHeaders($headers)->postJson('/api/calnest/events', [
            'title' => 'Site visit – Vaishnodevi',
            'description' => 'Carry the approved drawings',
            'location' => 'Vaishnodevi Circle, Ahmedabad',
            'start' => '2026-11-06T10:00:00+05:30',
            'end' => '2026-11-06T11:30:00+05:30',
            'is_all_day' => false,
            'color' => '#3B82F6',
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'title' => 'Site visit – Vaishnodevi',
                'description' => 'Carry the approved drawings',
                'location' => 'Vaishnodevi Circle, Ahmedabad',
                'start' => '2026-11-06T10:00:00+05:30',
                'end' => '2026-11-06T11:30:00+05:30',
                'is_all_day' => false,
                'color' => '#3B82F6',
            ]);

        $eventId = $createRes->json('id');
        $this->assertNotNull($eventId);

        // 2. Query in date range (matching)
        $listRes = $this->withHeaders($headers)
            ->getJson('/api/calnest/events?from=2026-11-01&to=2026-11-30');
        $listRes->assertStatus(200);
        $this->assertCount(1, $listRes->json('items'));

        // 3. Query in non-matching date range
        $emptyRes = $this->withHeaders($headers)
            ->getJson('/api/calnest/events?from=2026-12-01&to=2026-12-31');
        $emptyRes->assertStatus(200);
        $this->assertCount(0, $emptyRes->json('items'));

        // 4. Update Event
        $updateRes = $this->withHeaders($headers)->putJson('/api/calnest/events/' . $eventId, [
            'title' => 'Site visit – Vaishnodevi (Updated)',
        ]);
        $updateRes->assertStatus(200)
            ->assertJson(['title' => 'Site visit – Vaishnodevi (Updated)']);

        // 5. Delete Event
        $deleteRes = $this->withHeaders($headers)->deleteJson('/api/calnest/events/' . $eventId);
        $deleteRes->assertStatus(200)->assertJson(['status' => 'deleted']);
    }

    public function test_tasks_crud_and_partial_update(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Create Task
        $createRes = $this->withHeaders($headers)->postJson('/api/calnest/tasks', [
            'title' => 'Send RERA documents',
            'description' => 'PDF set to Surbhi',
            'priority' => 'high',
            'due' => '2026-11-08',
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'title' => 'Send RERA documents',
                'description' => 'PDF set to Surbhi',
                'priority' => 'high',
                'due' => '2026-11-08',
                'completed' => false,
            ]);

        $taskId = $createRes->json('id');

        // 2. Partial Update (marking completed only)
        $updateRes = $this->withHeaders($headers)->putJson('/api/calnest/tasks/' . $taskId, [
            'completed' => true,
        ]);
        $updateRes->assertStatus(200)
            ->assertJson([
                'id' => $taskId,
                'title' => 'Send RERA documents',
                'completed' => true,
            ]);

        // 3. Delete Task
        $deleteRes = $this->withHeaders($headers)->deleteJson('/api/calnest/tasks/' . $taskId);
        $deleteRes->assertStatus(200)->assertJson(['status' => 'deleted']);
    }

    public function test_manual_birthdays_crud(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Create Birthday
        $createRes = $this->withHeaders($headers)->postJson('/api/calnest/manual-birthdays', [
            'name' => 'Priya Shah',
            'date' => '1994-10-18',
            'phone' => '+919876543210',
            'relationship' => 'Friend',
            'notes' => null,
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'name' => 'Priya Shah',
                'date' => '1994-10-18',
                'phone' => '+919876543210',
                'relationship' => 'Friend',
            ]);

        $bdayId = $createRes->json('id');

        // 2. List Birthdays
        $listRes = $this->withHeaders($headers)->getJson('/api/calnest/manual-birthdays');
        $listRes->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, $listRes->json('total'));

        // 3. Update Birthday
        $updateRes = $this->withHeaders($headers)->putJson('/api/calnest/manual-birthdays/' . $bdayId, [
            'relationship' => 'Best Friend',
        ]);
        $updateRes->assertStatus(200)
            ->assertJson(['relationship' => 'Best Friend']);

        // 4. Delete Birthday
        $deleteRes = $this->withHeaders($headers)->deleteJson('/api/calnest/manual-birthdays/' . $bdayId);
        $deleteRes->assertStatus(200)->assertJson(['status' => 'deleted']);
    }

    public function test_preferences_and_reminder_settings(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Save Preferences
        $prefRes = $this->withHeaders($headers)->postJson('/api/calnest/preferences', [
            'theme' => 'dark',
            'calendar_view' => 'month',
        ]);
        $prefRes->assertStatus(200)
            ->assertJson([
                'preferences' => [
                    'theme' => 'dark',
                    'calendar_view' => 'month',
                ],
            ]);

        // 2. Save Reminder Settings (typed response)
        $reminderRes = $this->withHeaders($headers)->postJson('/api/calnest/reminder-settings', [
            'event_reminder_minutes' => 15,
            'birthday_reminder_enabled' => true,
            'birthday_reminder_time' => '09:00',
        ]);
        $reminderRes->assertStatus(200)
            ->assertJson([
                'event_reminder_minutes' => 15,
                'birthday_reminder_enabled' => true,
                'birthday_reminder_time' => '09:00',
            ]);

        // 3. Read Reminder Settings
        $getReminder = $this->withHeaders($headers)->getJson('/api/calnest/reminder-settings');
        $getReminder->assertStatus(200)
            ->assertJson([
                'event_reminder_minutes' => 15,
                'birthday_reminder_enabled' => true,
                'birthday_reminder_time' => '09:00',
            ]);
    }

    public function test_password_reset_and_email_verification(): void
    {
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        // 1. Forgot password
        $this->postJson('/api/calnest/forgot-password', [
            'email' => 'demo@example.com',
        ])->assertStatus(200)->assertJson(['status' => 'sent']);

        $codeRecord = CalnestVerificationCode::where('email', 'demo@example.com')
            ->where('type', 'reset_password')
            ->first();
        $this->assertNotNull($codeRecord);

        // 2. Reset password
        $resetRes = $this->postJson('/api/calnest/reset-password', [
            'email' => 'demo@example.com',
            'code' => $codeRecord->code,
            'password' => 'brandnewpassword',
        ]);
        $resetRes->assertStatus(200)->assertJson(['status' => 'ok']);

        // Check user can login with new password
        $this->postJson('/api/calnest/login', [
            'email' => 'demo@example.com',
            'password' => 'brandnewpassword',
        ])->assertStatus(200);

        // 3. Email verification
        CalnestVerificationCode::create([
            'email' => 'demo@example.com',
            'code' => '654321',
            'type' => 'verify_email',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $verifyRes = $this->postJson('/api/calnest/verify-email', [
            'email' => 'demo@example.com',
            'code' => '654321',
        ]);
        $verifyRes->assertStatus(200)->assertJson(['status' => 'verified']);
    }

    public function test_user_scoping_and_cross_user_isolation(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tokenA = $userA->createToken('testA')->plainTextToken;
        $tokenB = $userB->createToken('testB')->plainTextToken;

        // User A creates an event
        $eventA = CalnestEvent::create([
            'user_id' => $userA->id,
            'title' => "User A Event",
            'start' => '2026-11-06T10:00:00+05:30',
            'end' => '2026-11-06T11:00:00+05:30',
            'is_all_day' => false,
        ]);

        // User B tries to update User A's event -> 404
        $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->putJson('/api/calnest/events/' . $eventA->id, ['title' => 'Hacked'])
            ->assertStatus(404);

        // User B tries to delete User A's event -> 404
        $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->deleteJson('/api/calnest/events/' . $eventA->id)
            ->assertStatus(404);

        // User B listing events does not see User A's event
        $listRes = $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->getJson('/api/calnest/events');
        $this->assertCount(0, $listRes->json('items'));
    }

    public function test_greetings_library_endpoint_and_filtering(): void
    {
        $this->seed(\Database\Seeders\CalnestGreetingSeeder::class);

        // 1. Public list without token
        $res = $this->getJson('/api/calnest/greetings');
        $res->assertStatus(200)
            ->assertJsonStructure([
                'items' => [
                    '*' => [
                        'id', 'title', 'message', 'category', 'emoji',
                        'language', 'author', 'image_url', 'tags', 'created_at',
                    ],
                ],
            ]);
        $this->assertGreaterThanOrEqual(30, count($res->json('items')));

        // 2. Filter by category (birthday)
        $bdayRes = $this->getJson('/api/calnest/greetings?category=birthday');
        $bdayRes->assertStatus(200);
        foreach ($bdayRes->json('items') as $item) {
            $this->assertEquals('birthday', $item['category']);
        }

        // 3. Filter with camelCase and snake_case (goodMorning vs good_morning)
        $gmRes1 = $this->getJson('/api/calnest/greetings?category=goodMorning');
        $gmRes1->assertStatus(200);
        $this->assertNotEmpty($gmRes1->json('items'));

        $gmRes2 = $this->getJson('/api/calnest/greetings?category=good_morning');
        $gmRes2->assertStatus(200);
        $this->assertNotEmpty($gmRes2->json('items'));
    }
}
