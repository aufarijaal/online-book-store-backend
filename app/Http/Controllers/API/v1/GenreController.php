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

    public function getBookByGenre(Request $r, string $genreSlug)
    {
        $sortBy = $r->query('sortBy') ?? "title";
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';

        $books = \App\Models\Book::select([
            'books.id',
            'books.author_id',
            'books.title',
            'books.price',
            'books.cover_image',
            'books.slug',
            'authors.name as author_name',
            'authors.slug as author_slug',
        ])->whereRelation('genre', 'slug', '=', $genreSlug)
            ->leftJoin('authors', 'authors.id', '=', 'books.author_id')
            ->groupBy(
                'books.id',
                'books.author_id',
                'books.title',
                'books.price',
                'books.cover_image',
                'books.slug',
                'authors.name',
                'authors.slug'
            )
            ->orderBy($sortBy, $sortDirection)
            ->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $books,
        ]);
    }
}