<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forHomePage')) {
            $books = \App\Models\Book::select([
                'books.id',
                'books.author_id',
                'books.title',
                'books.price',
                'books.cover_image',
                'books.slug',
                'authors.name as author_name',
                'authors.slug as author_slug',
            ])->where('stock_qty', '>', 0)
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
                ->take(20)
                ->get();
            return response()->json([
                'message' => 'OK',
                'data' => $books
            ]);
        }
    }

    public function getOneBySlug(string $slug)
    {
        $book = \App\Models\Book::where('slug', $slug)->with(['author:id,name,slug', 'genre:id,name'])->get()->first();

        return response()->json([
            'message' => 'OK',
            'data' => $book
        ]);
    }

    public function search(Request $r)
    {
        $sortBy = $r->query('sortBy') ?? "title";
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $books = \App\Models\Book::select([
            'books.id',
            'books.author_id',
            'books.title',
            'books.price',
            'books.cover_image',
            'books.slug',
            'authors.name as author_name',
            'authors.slug as author_slug',
        ])->where(function ($query) use ($q) {
            $query
                ->where('books.title', 'LIKE', '%' . $q . '%')
                ->orWhere('authors.name', 'LIKE', '%' . $q . '%');
        })
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