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
        $author = \App\Models\Author::where("slug", $authorSlug)->get()->first();

        return response()->json([
            'message' => 'OK',
            'data' => $author
        ]);
    }
}
