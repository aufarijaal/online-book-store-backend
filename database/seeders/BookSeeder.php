<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $booksFromFile = file_get_contents('database/static/books.json');
        $booksDecoded = json_decode($booksFromFile);

        $booksToInsert = [];

        foreach ($booksDecoded as $book) {
            array_push(
                $booksToInsert,
                [
                    'author_id' => \App\Models\Author::all()->random()->id,
                    'genre_id' => $book->categoryId,
                    'title' => $book->title,
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
                    'cover_image' => $book->cover->filename . '.jpg',
                    'slug' => \Illuminate\Support\Str::slug($book->title . '-' . \Illuminate\Support\Str::random(10)),
                    'created_at' => now()
                ]
            );
        }

        \App\Models\Book::insert($booksToInsert);
    }
}
