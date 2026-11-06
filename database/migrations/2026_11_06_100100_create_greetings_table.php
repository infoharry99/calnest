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
        if (!Schema::hasTable('greetings')) {
            Schema::create('greetings', function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->text('message');
                $t->string('category', 40);
                $t->string('emoji', 16)->nullable();
                $t->string('language', 40)->default('English');
                $t->string('author', 80)->default('CalNest');
                $t->string('image_url')->nullable();
                $t->json('tags')->nullable();
                $t->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('greetings');
    }
};
