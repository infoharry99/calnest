<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Google Calendars
        Schema::create('google_calendars', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "user@gmail.com"
            $table->string('summary');
            $table->boolean('primary')->default(false);
            $table->string('accessRole')->default('owner');
            $table->string('backgroundColor')->nullable();
            $table->string('colorId')->nullable();
            $table->timestamps();
        });

        // 2. Google Calendar Events
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "7k3c9qa1b2"
            $table->string('calendar_id');
            $table->string('status')->default('confirmed');
            $table->string('summary');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('htmlLink')->nullable();
            $table->string('start_dateTime')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_dateTime')->nullable();
            $table->string('end_date')->nullable();
            $table->string('colorId')->nullable();
            $table->timestamps();

            $table->foreign('calendar_id')->references('id')->on('google_calendars')->cascadeOnDelete();
        });

        // 3. Google Task Lists
        Schema::create('google_task_lists', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "MTIzNDU2"
            $table->string('title');
            $table->string('updated_at_iso')->nullable();
            $table->timestamps();
        });

        // 4. Google Tasks
        Schema::create('google_tasks', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "dGFzay0x"
            $table->string('task_list_id');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->string('status')->default('needsAction'); // 'needsAction' or 'completed'
            $table->string('due')->nullable();
            $table->string('completed')->nullable();
            $table->timestamps();

            $table->foreign('task_list_id')->references('id')->on('google_task_lists')->cascadeOnDelete();
        });

        // 5. Google Contacts (People API)
        Schema::create('google_contacts', function (Blueprint $table) {
            $table->string('resourceName')->primary(); // e.g. "people/c1234567890"
            $table->string('displayName');
            $table->string('givenName')->nullable();
            $table->string('familyName')->nullable();
            $table->integer('birthday_year')->nullable();
            $table->integer('birthday_month')->nullable();
            $table->integer('birthday_day')->nullable();
            $table->string('phone_value')->nullable();
            $table->string('phone_type')->default('mobile');
            $table->text('photo_url')->nullable();
            $table->timestamps();
        });

        // 6. Microsoft Account Profile
        Schema::create('microsoft_accounts', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "a1b2c3d4"
            $table->string('displayName');
            $table->string('mail')->nullable();
            $table->string('userPrincipalName');
            $table->timestamps();
        });

        // 7. Microsoft Calendars
        Schema::create('microsoft_calendars', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "AAMkAGI2"
            $table->string('name');
            $table->boolean('isDefaultCalendar')->default(false);
            $table->string('color')->default('auto');
            $table->timestamps();
        });

        // 8. Microsoft Calendar Events
        Schema::create('microsoft_calendar_events', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "AAMkAGI2-evt1"
            $table->string('calendar_id');
            $table->string('subject');
            $table->boolean('isAllDay')->default(false);
            $table->string('start_dateTime');
            $table->string('start_timeZone')->default('UTC');
            $table->string('end_dateTime');
            $table->string('end_timeZone')->default('UTC');
            $table->string('location_displayName')->nullable();
            $table->text('bodyPreview')->nullable();
            $table->text('body_content')->nullable();
            $table->string('body_contentType')->default('text');
            $table->timestamps();

            $table->foreign('calendar_id')->references('id')->on('microsoft_calendars')->cascadeOnDelete();
        });

        // 9. Microsoft To Do Lists
        Schema::create('microsoft_todo_lists', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "AQMkADAw"
            $table->string('displayName');
            $table->boolean('isOwner')->default(true);
            $table->string('wellknownListName')->nullable()->default('defaultList');
            $table->timestamps();
        });

        // 10. Microsoft To Do Tasks
        Schema::create('microsoft_todo_tasks', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "AQMkADAw-t1"
            $table->string('list_id');
            $table->string('title');
            $table->string('status')->default('notStarted'); // notStarted, completed
            $table->text('body_content')->nullable();
            $table->string('body_contentType')->default('text');
            $table->string('due_dateTime')->nullable();
            $table->string('due_timeZone')->default('UTC');
            $table->timestamps();

            $table->foreign('list_id')->references('id')->on('microsoft_todo_lists')->cascadeOnDelete();
        });

        // 11. CalNest Native Backend Storage (Device Local Storage Sync)
        Schema::create('calnest_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('calnest_manual_birthdays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('date'); // YYYY-MM-DD or MM-DD
            $table->string('phone')->nullable();
            $table->string('relationship')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('calnest_selected_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('calendar_id');
            $table->enum('provider', ['google', 'microsoft']);
            $table->boolean('is_selected')->default(true);
            $table->timestamps();

            $table->unique(['calendar_id', 'provider']);
        });

        Schema::create('calnest_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->string('setting_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calnest_reminder_settings');
        Schema::dropIfExists('calnest_selected_calendars');
        Schema::dropIfExists('calnest_manual_birthdays');
        Schema::dropIfExists('calnest_preferences');
        Schema::dropIfExists('microsoft_todo_tasks');
        Schema::dropIfExists('microsoft_todo_lists');
        Schema::dropIfExists('microsoft_calendar_events');
        Schema::dropIfExists('microsoft_calendars');
        Schema::dropIfExists('microsoft_accounts');
        Schema::dropIfExists('google_contacts');
        Schema::dropIfExists('google_tasks');
        Schema::dropIfExists('google_task_lists');
        Schema::dropIfExists('google_calendar_events');
        Schema::dropIfExists('google_calendars');
    }
};
