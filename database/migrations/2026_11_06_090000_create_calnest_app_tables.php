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
        // 1. CalNest Native Events
        Schema::create('calnest_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('start'); // ISO 8601 with offset
            $table->string('end');   // ISO 8601 with offset
            $table->boolean('is_all_day')->default(false);
            $table->string('color')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start']);
        });

        // 2. CalNest Native Tasks
        Schema::create('calnest_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('due')->nullable(); // YYYY-MM-DD
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'completed']);
        });

        // 3. Email Verification & Password Reset Codes
        Schema::create('calnest_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('code', 6);
            $table->string('type'); // 'reset_password', 'verify_email'
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // 4. Update calnest_manual_birthdays with user_id
        if (Schema::hasTable('calnest_manual_birthdays') && !Schema::hasColumn('calnest_manual_birthdays', 'user_id')) {
            Schema::table('calnest_manual_birthdays', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        // 5. Update calnest_preferences with user_id & compound unique
        if (Schema::hasTable('calnest_preferences') && !Schema::hasColumn('calnest_preferences', 'user_id')) {
            Schema::table('calnest_preferences', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
            // Try dropping old unique if possible depending on driver
            try {
                Schema::table('calnest_preferences', function (Blueprint $table) {
                    $table->dropUnique(['key']);
                });
            } catch (\Throwable $e) {
                // Ignore if unique index name differs or not supported
            }
        }

        // 6. Update calnest_reminder_settings with user_id
        if (Schema::hasTable('calnest_reminder_settings') && !Schema::hasColumn('calnest_reminder_settings', 'user_id')) {
            Schema::table('calnest_reminder_settings', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
            try {
                Schema::table('calnest_reminder_settings', function (Blueprint $table) {
                    $table->dropUnique(['setting_key']);
                });
            } catch (\Throwable $e) {
                // Ignore if unique index name differs or not supported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calnest_verification_codes');
        Schema::dropIfExists('calnest_tasks');
        Schema::dropIfExists('calnest_events');

        if (Schema::hasTable('calnest_manual_birthdays') && Schema::hasColumn('calnest_manual_birthdays', 'user_id')) {
            Schema::table('calnest_manual_birthdays', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasTable('calnest_preferences') && Schema::hasColumn('calnest_preferences', 'user_id')) {
            Schema::table('calnest_preferences', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasTable('calnest_reminder_settings') && Schema::hasColumn('calnest_reminder_settings', 'user_id')) {
            Schema::table('calnest_reminder_settings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
