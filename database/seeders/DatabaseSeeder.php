<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\User::factory(3)->create([
            'is_admin' => true
        ]);

        $this->call([
            AuthorSeeder::class,
            GenreSeeder::class,
            AddressSeeder::class,
            BookSeeder::class,
            OrderSeeder::class
        ]);
    }
}
