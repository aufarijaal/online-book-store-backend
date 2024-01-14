<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('getGenreForNav')) {
            $genres = \App\Models\Genre::get(['id', 'name', 'slug']);

            return response()->json([
                'message' => 'OK',
                'data' => $genres
            ]);
        }
    }

    public function getBookByGenre(string $genreSlug)
    {
        $books = \App\Models\Book::with(['author:id,name', 'genre:id,name,slug'])->whereRelation('genre', 'slug', '=', $genreSlug)->get();

        $books->each(function ($book) {
            if (!is_null($book->cover_image)) {
                $book->cover_image = asset('storage/covers/' . $book->cover_image);
            }
        });

        return response()->json([
            'message' => 'OK',
            'data' => $books
        ]);
    }
}
