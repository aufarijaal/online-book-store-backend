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
            [
                "id" => 1,
                "name" => "Arts & Photography",
                "slug" => "arts-and-photography",
            ],
            [
                "id" => 2,
                "name" => "Biographies & Memoirs",
                "slug" => "biographies-and-memoirs",
            ],
            [
                "id" => 3,
                "name" => "Business & Investing",
                "slug" => "business-and-investing",
            ],
            [
                "id" => 4,
                "name" => "Calendars",
                "slug" => "calendars",
            ],
            [
                "id" => 5,
                "name" => "Children\'s Books",
                "slug" => "childrens-books",
            ],
            [
                "id" => 6,
                "name" => "Comics & Graphic Novels",
                "slug" => "comics-and-graphic-novels",
            ],
            [
                "id" => 7,
                "name" => "Computers & Internet",
                "slug" => "computers-and-internet",
            ],
            [
                "id" => 8,
                "name" => "Entertainment",
                "slug" => "entertainment",
            ],
            [
                "id" => 9,
                "name" => "Health, Mind & Body",
                "slug" => "health-mind-and-body",
            ],
            [
                "id" => 10,
                "name" => "History",
                "slug" => "history",
            ],
            [
                "id" => 11,
                "name" => "Home & Garden",
                "slug" => "home-and-garden",
            ],
            [
                "id" => 12,
                "name" => "Law",
                "slug" => "law",
            ],
            [
                "id" => 13,
                "name" => "Literature & Fiction",
                "slug" => "literature-and-fiction",
            ],
            [
                "id" => 14,
                "name" => "Medicine",
                "slug" => "medicine",
            ],
            [
                "id" => 15,
                "name" => "Mystery & Thrillers",
                "slug" => "mystery-and-thrillers",
            ],
            [
                "id" => 16,
                "name" => "Nonfiction",
                "slug" => "nonfiction",
            ],
            [
                "id" => 17,
                "name" => "Outdoors & Nature",
                "slug" => "outdoors-and-nature",
            ],
            [
                "id" => 18,
                "name" => "Parenting & Families",
                "slug" => "parenting-and-families",
            ],
            [
                "id" => 19,
                "name" => "Professional & Technical",
                "slug" => "professional-and-technical",
            ],
            [
                "id" => 20,
                "name" => "Reference",
                "slug" => "reference",
            ],
            [
                "id" => 21,
                "name" => "Religion & Spirituality",
                "slug" => "religion-and-spirituality",
            ],
            [
                "id" => 22,
                "name" => "Romance",
                "slug" => "romance",
            ],
            [
                "id" => 23,
                "name" => "Science",
                "slug" => "science",
            ],
            [
                "id" => 24,
                "name" => "Science Fiction & Fantasy",
                "slug" => "science-fiction-and-fantasy",
            ],
            [
                "id" => 25,
                "name" => "Sports",
                "slug" => "sports",
            ],
            [
                "id" => 26,
                "name" => "Teens",
                "slug" => "teens",
            ],
            [
                "id" => 27,
                "name" => "Travel",
                "slug" => "travel",
            ],
        ];

        for ($i = 0; $i < count($genres); $i++) {
            $genres[$i]['description'] = fake()->sentences(3, true);
            $genres[$i]['created_at'] = now();
        }

        \App\Models\Genre::insert($genres);
    }
}
