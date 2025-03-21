<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create a test user if none exists
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        if (!$user) {
            $user = \App\Models\User::factory()->create([
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Call the snippet seeder
        $this->call(SnippetSeeder::class);
    }
}
