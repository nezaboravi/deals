<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     */
    public function run(): void
    {
        // Run our production seeders in the correct order
        $this->call([
            CategorySeeder::class,
            DealSeeder::class,
            VoteSeeder::class,
        ]);
    }
}
