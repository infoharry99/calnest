<?php

namespace Database\Seeders;

use App\Models\Greeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GreetingSeeder extends Seeder
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

        $rows = json_decode(File::get($path), true);
        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as $r) {
            Greeting::updateOrCreate(
                ['id' => $r['id']],
                [
                    'title' => $r['title'],
                    'message' => $r['message'],
                    'category' => $r['category'],
                    'emoji' => $r['emoji'] ?? null,
                    'language' => $r['language'] ?? 'English',
                    'author' => $r['author'] ?? 'CalNest',
                    'image_url' => $r['image_url'] ?? null,
                    'tags' => $r['tags'] ?? [],
                    'created_at' => $r['created_at'] ?? now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
