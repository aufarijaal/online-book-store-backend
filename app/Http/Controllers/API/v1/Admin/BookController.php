<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forDropdown')) {
            $books = \App\Models\Book::with(['author:id,name'])->get(['id', 'author_id', 'title', 'price', 'stock_qty', 'cover_image']);

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

        $sizePerPage = $r->query('sizePerPage') ?? 20;
        $books = $r->query('q') ?
            \App\Models\Book::where('title', 'LIKE', '%' . $r->query('q') . '%')->paginate($sizePerPage) :
            \App\Models\Book::paginate($sizePerPage);

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

    public function edit(string $id)
    {
        $book = \App\Models\Book::find($id);

        if (!is_null($book->cover_image)) {
            $book->cover_image = asset('storage/covers/' . $book->cover_image);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $book
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $r->validate([
            'author_id' => 'required|numeric|integer|exists:authors,id',
            'genre_id' => 'required|numeric|integer|exists:genres,id',
            'title' => 'required|string|max:255',
            'published_date' => 'required|string|date_format:Y-m-d',
            'stock_qty' => 'required|numeric|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $data = [...$r->only(['author_id', 'genre_id', 'title', 'published_date', 'stock_qty', 'price'])];
        $coverName = null;

        if ($r->hasFile('cover_image')) {
            $r->validate([
                'cover_image' => 'image'
            ]);
            $cover = $r->file('cover_image');
            $coverName = sprintf(
                '%s-%s-%s.%s',
                \Illuminate\Support\Str::kebab(\App\Models\Author::where('id', $r->input('author_id'))->get()->first()->name),
                \Illuminate\Support\Str::kebab($r->input('title')),
                \Illuminate\Support\Str::random(32),
                $cover->getClientOriginalExtension()
            );
            $cover->storePubliclyAs('covers', $coverName, 'public');
            $data['cover_image'] = $coverName;
        }

        \App\Models\Book::create($data);
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function update(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'id' => 'required|numeric|integer|exists:books,id',
            'author_id' => 'numeric|integer|exists:authors,id',
            'genre_id' => 'numeric|integer|exists:genres,id',
            'title' => 'string|max:255',
            'published_date' => 'string|date_format:Y-m-d',
            'stock_qty' => 'numeric|integer|min:0',
            'price' => 'numeric|min:0',
        ]);

        $data = [...$validated];
        $coverName = null;

        if ($r->hasFile('cover_image')) {
            $r->validate([
                'cover_image' => 'image'
            ]);
            $cover = $r->file('cover_image');
            $coverName = sprintf(
                '%s-%s-%s.%s',
                \Illuminate\Support\Str::kebab(\App\Models\Author::where('id', $r->input('author_id'))->get()->first()->name),
                \Illuminate\Support\Str::kebab($r->input('title')),
                \Illuminate\Support\Str::random(32),
                $cover->getClientOriginalExtension()
            );
            $cover->storePubliclyAs('covers', $coverName, 'public');
            $data['cover_image'] = $coverName;

            // Remove the existing one
            \Illuminate\Support\Facades\Storage::delete('public/covers/' . \App\Models\Book::find($validated['id'])->cover_image);
        }

        \App\Models\Book::where('id', $validated['id'])->update($data);
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function destroy(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $books = \App\Models\Book::whereIn('id', explode(',', $r->query('ids')))->get();

        $books->each(function ($book) {
            \Illuminate\Support\Facades\Storage::delete('public/covers/' . $book->cover_image);
        });
        \App\Models\Book::destroy(explode(',', $r->query('ids')));

        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}
