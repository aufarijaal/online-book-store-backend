<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
    }

    public function show(string $authorSlug)
    {
        $author = \App\Models\Author::with(['books'])->where("slug", $authorSlug)->get()->first();

        $author->books->each(function ($book) {
            if (!is_null($book->cover_image)) {
                $book->cover_image = asset('storage/covers/' . $book->cover_image);
            }
        });

        return response()->json([
            'message' => 'OK',
            'data' => $author
        ]);
    }
}
