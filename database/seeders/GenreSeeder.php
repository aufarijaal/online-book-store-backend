<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['name' => 'Fiction', 'description' => fake()->sentences(3, true)],
            ['name' => 'Mystery', 'description' => fake()->sentences(3, true)],
            ['name' => 'Science Fiction', 'description' => fake()->sentences(3, true)],
            ['name' => 'Romance', 'description' => fake()->sentences(3, true)],
            ['name' => 'Fantasy', 'description' => fake()->sentences(3, true)],
            ['name' => 'Thriller', 'description' => fake()->sentences(3, true)],
            ['name' => 'Horror', 'description' => fake()->sentences(3, true)],
            ['name' => 'Non-Fiction', 'description' => fake()->sentences(3, true)],
            ['name' => 'Historical Fiction', 'description' => fake()->sentences(3, true)],
            ['name' => 'Biography', 'description' => fake()->sentences(3, true)],
            ['name' => 'Self-Help', 'description' => fake()->sentences(3, true)],
            ['name' => 'Business', 'description' => fake()->sentences(3, true)],
            ['name' => 'Humor', 'description' => fake()->sentences(3, true)],
            ['name' => 'Adventure', 'description' => fake()->sentences(3, true)],
            ['name' => 'Children', 'description' => fake()->sentences(3, true)],
            ['name' => 'Young Adult', 'description' => fake()->sentences(3, true)],
            ['name' => 'Poetry', 'description' => fake()->sentences(3, true)],
            ['name' => 'Drama', 'description' => fake()->sentences(3, true)],
            ['name' => 'Crime', 'description' => fake()->sentences(3, true)],
        ];

        for ($i = 0; $i < count($genres); $i++) {
            $genres[$i]['slug'] =  \Illuminate\Support\Str::slug($genres[$i]['name']);
        }

        \App\Models\Genre::insert($genres);
    }
}
