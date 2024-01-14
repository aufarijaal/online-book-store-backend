<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => \App\Models\Author::all()->random()->id,
            'genre_id' => \App\Models\Genre::all()->random()->id,
            'title' => \Illuminate\Support\Str::title(fake()->words(fake()->numberBetween(1, 5), true)),
            'published_date' => fake()->date(),
            'price' => fake()->randomElement([
                98000,
                100000,
                320000,
                170000,
                90500,
                235000,
            ]),
            'stock_qty' => fake()->numberBetween(0, 200),
            'cover_image' => null
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (\App\Models\Book $book) {
            $book->slug = \Illuminate\Support\Str::slug($book->title);
        });
    }
}
