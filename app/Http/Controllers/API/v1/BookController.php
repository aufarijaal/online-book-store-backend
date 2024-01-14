<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forHomePage')) {
            $books = \App\Models\Book::where("stock_qty", ">", 0)->with(['author:id,name,slug'])->get(['id', 'author_id', 'title', 'price', 'cover_image', 'slug'])->take(20);

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

    public function getOneBySlug(string $slug)
    {
        $book = \App\Models\Book::where('slug', $slug)->with(['author:id,name,slug', 'genre:id,name'])->get()->first();

        if (!is_null($book->cover_image)) {
            $book->cover_image = asset('storage/covers/' . $book->cover_image);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $book
        ]);
    }

    public function search(Request $r)
    {
        $books = \App\Models\Book::with(['author:id,name,slug'])
            ->where('title', 'LIKE', '%' . $r->query('q') . '%')
            ->orWhereRelation('author', 'name', 'LIKE', '%' . $r->query('q') . '%')
            ->get(['id', 'author_id', 'title', 'price', 'cover_image', 'slug']);

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
