<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            CalendarEventSeeder::class,
            NewsSeeder::class,
            ContactSeeder::class,
            BannerImageSeeder::class,
        ]);
    }
}
