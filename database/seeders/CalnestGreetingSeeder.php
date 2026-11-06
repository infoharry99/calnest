<?php

namespace Database\Seeders;

use App\Models\CalnestGreeting;
use App\Models\Greeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CalnestGreetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/calnest_greetings_seed.json');
        if (!File::exists($path)) {
            $path = base_path('calnest_greetings_seed.json');
        }

        if (!File::exists($path)) {
            return;
        }

        $items = json_decode(File::get($path), true);
        if (!is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            $data = [
                'title' => $item['title'],
                'message' => $item['message'],
                'category' => $item['category'],
                'emoji' => $item['emoji'] ?? null,
                'language' => $item['language'] ?? 'English',
                'author' => $item['author'] ?? 'CalNest',
                'image_url' => $item['image_url'] ?? null,
                'tags' => $item['tags'] ?? [],
                'created_at' => $item['created_at'] ?? now(),
                'updated_at' => now(),
            ];

            Greeting::updateOrCreate(['id' => $item['id']], $data);
            if (\Illuminate\Support\Facades\Schema::hasTable('calnest_greetings')) {
                CalnestGreeting::updateOrCreate(['id' => $item['id']], $data);
            }
        }
    }
}
