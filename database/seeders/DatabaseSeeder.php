<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\TeamSeeder;
use Database\Seeders\CollaborateHereSeeder;
use Database\Seeders\WhatUpDetailSeeder;
use Database\Seeders\WthIsThisSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            TeamSeeder::class,
            CollaborateHereSeeder::class,
            WhatUpDetailSeeder::class,
            WthIsThisSeeder::class,
        ]);
    }
}
