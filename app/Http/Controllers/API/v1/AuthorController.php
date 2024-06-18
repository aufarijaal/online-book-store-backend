<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
    }

    public function show(Request $r, string $authorSlug)
    {
        $page = $r->query('page') ?? 1;

        $author = \App\Models\Author::select(['id', 'name', 'dob', 'nationality'])->where('slug', '=', $authorSlug)->get()->first();

        $books = \App\Models\Book::select([
            'books.id',
            'books.author_id',
            'books.title',
            'books.price',
            'books.cover_image',
            'books.slug',
            'authors.name as author_name',
            'authors.slug as author_slug',
        ])->whereRelation('author', 'slug', '=', $authorSlug)
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
            ->orderBy('books.title', 'asc')
            ->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => [
                'author' => $author,
                'books' => $books,
            ],
        ]);
    }
}