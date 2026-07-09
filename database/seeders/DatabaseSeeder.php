<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $client = \App\Models\Client::create([
            'name' => 'Test Client',
            'license_key' => 'test-license-key',
            'status' => 'active'
        ]);

        $urls = ['/security', '/how-it-works', '/', '/admin/dashboard', '/pricing'];

        // Create today's logs (about 40)
        for ($i=0; $i<40; $i++) {
            \App\Models\VisitorLog::create([
                'client_id' => $client->id,
                'ip_address' => rand(10,255).'.'.rand(10,255).'.'.rand(10,255).'.'.rand(10,255),
                'session_id' => \Illuminate\Support\Str::random(40),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'browser' => 'Chrome',
                'device' => 'Desktop',
                'page_url' => $urls[array_rand($urls)],
                'visited_at' => now()->subHours(rand(0, 23))->subMinutes(rand(1, 59))
            ]);
        }
        
        // Create this month's logs (about 50)
        for ($i=0; $i<50; $i++) {
            \App\Models\VisitorLog::create([
                'client_id' => $client->id,
                'ip_address' => rand(10,255).'.'.rand(10,255).'.'.rand(10,255).'.'.rand(10,255),
                'session_id' => \Illuminate\Support\Str::random(40),
                'user_agent' => 'Mozilla/5.0',
                'browser' => 'Firefox',
                'device' => 'Mobile',
                'page_url' => $urls[array_rand($urls)],
                'visited_at' => now()->subDays(rand(1, 25))->subHours(rand(0, 23))
            ]);
        }
    }
}
