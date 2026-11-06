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
        Schema::create('calnest_greetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('category')->index();
            $table->string('emoji', 32)->nullable();
            $table->string('language')->default('English');
            $table->string('author')->nullable()->default('CalNest');
            $table->text('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calnest_greetings');
    }
};
