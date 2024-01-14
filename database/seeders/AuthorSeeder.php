<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ["name" => "Stephen King", "dob" => "1947-09-21", "nationality" => "American", "slug" => "stephen-king"],
            ["name" => "J.K. Rowling", "dob" => "1965-07-31", "nationality" => "British", "slug" => "jk-rowling"],
            ["name" => "Agatha Christie", "dob" => "1890-09-15", "nationality" => "British", "slug" => "agatha-christie"],
            ["name" => "George R.R. Martin", "dob" => "1948-09-20", "nationality" => "American", "slug" => "george-rr-martin"],
            ["name" => "J.R.R. Tolkien", "dob" => "1892-01-03", "nationality" => "British", "slug" => "jrr-tolkien"],
            ["name" => "Jane Austen", "dob" => "1775-12-16", "nationality" => "British", "slug" => "jane-austen"],
            ["name" => "Mark Twain", "dob" => "1835-11-30", "nationality" => "American", "slug" => "mark-twain"],
            ["name" => "Charles Dickens", "dob" => "1812-02-07", "nationality" => "British", "slug" => "charles-dickens"],
            ["name" => "Leo Tolstoy", "dob" => "1828-09-09", "nationality" => "Russian", "slug" => "leo-tolstoy"],
            ["name" => "William Shakespeare", "dob" => "1564-04-23", "nationality" => "British", "slug" => "william-shakespeare"],
            ["name" => "Ernest Hemingway", "dob" => "1899-07-21", "nationality" => "American", "slug" => "ernest-hemingway"],
            ["name" => "Virginia Woolf", "dob" => "1882-01-25", "nationality" => "British", "slug" => "virginia-woolf"],
            ["name" => "Toni Morrison", "dob" => "1931-02-18", "nationality" => "American", "slug" => "toni-morrison"],
            ["name" => "Haruki Murakami", "dob" => "1949-01-12", "nationality" => "Japanese", "slug" => "haruki-murakami"],
            ["name" => "Gabriel García Márquez", "dob" => "1927-03-06", "nationality" => "Colombian", "slug" => "gabriel-garcia-marquez"],
            ["name" => "Fyodor Dostoevsky", "dob" => "1821-11-11", "nationality" => "Russian", "slug" => "fyodor-dostoevsky"],
            ["name" => "Dan Brown", "dob" => "1964-06-22", "nationality" => "American", "slug" => "dan-brown"],
            ["name" => "Roald Dahl", "dob" => "1916-09-13", "nationality" => "British", "slug" => "roald-dahl"],
            ["name" => "J.D. Salinger", "dob" => "1919-01-01", "nationality" => "American", "slug" => "jd-salinger"],
            ["name" => "H.G. Wells", "dob" => "1866-09-21", "nationality" => "British", "slug" => "hg-wells"],
            ["name" => "Emily Dickinson", "dob" => "1830-12-10", "nationality" => "American", "slug" => "emily-dickinson"],
            ["name" => "Edgar Allan Poe", "dob" => "1809-01-19", "nationality" => "American", "slug" => "edgar-allan-poe"],
            ["name" => "Herman Melville", "dob" => "1819-08-01", "nationality" => "American", "slug" => "herman-melville"],
            ["name" => "Kurt Vonnegut", "dob" => "1922-11-11", "nationality" => "American", "slug" => "kurt-vonnegut"],
            ["name" => "Miguel de Cervantes", "dob" => "1547-09-29", "nationality" => "Spanish", "slug" => "miguel-de-cervantes"],
            ["name" => "John Steinbeck", "dob" => "1902-02-27", "nationality" => "American", "slug" => "john-steinbeck"],
            ["name" => "George Orwell", "dob" => "1903-06-25", "nationality" => "British", "slug" => "george-orwell"],
            ["name" => "F. Scott Fitzgerald", "dob" => "1896-09-24", "nationality" => "American", "slug" => "f-scott-fitzgerald"],
            ["name" => "Oscar Wilde", "dob" => "1854-10-16", "nationality" => "Irish", "slug" => "oscar-wilde"],
            ["name" => "Rabindranath Tagore", "dob" => "1861-05-07", "nationality" => "Indian", "slug" => "rabindranath-tagore"]
        ];


        collect($authors)->each(function ($author) {
            \App\Models\Author::create($author);
        });
    }
}
