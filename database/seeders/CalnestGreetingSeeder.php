<?php

namespace Database\Seeders;

use App\Models\CalnestGreeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CalnestGreetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = base_path('calnest_greetings_seed.json');

        if (!File::exists($jsonPath)) {
            return;
        }

        $items = json_decode(File::get($jsonPath), true);
        if (!is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            CalnestGreeting::updateOrCreate(
                ['id' => $item['id']],
                [
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
                ]
            );
        }
    }
}
