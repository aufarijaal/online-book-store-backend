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

        $sortBy = $r->query('sortBy') ?? "id";
        $dataPerPage = $r->query('dataPerPage') ?? 20;
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $booksCount = \App\Models\Book::count();
        $books = \App\Models\Book::select([
            'books.id',
            'books.author_id',
            'books.genre_id',
            'books.title',
            'books.published_date',
            'books.price',
            'books.stock_qty',
            'books.created_at',
            'books.cover_image',
            'authors.name as author_name',
            'genres.name as genre_name'
        ])->where(function ($query) use ($q) {
            $query
                ->where('books.title', 'LIKE', '%' . $q . '%')
                ->where('genres.name', 'LIKE', '%' . $q . '%')
                ->orWhere('authors.name', 'LIKE', '%' . $q . '%');
        })
            ->join('genres', 'books.genre_id', '=', 'genres.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($dataPerPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $books,
            'count' => $booksCount
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

        $data = [
            ...$r->only(['author_id', 'genre_id', 'title', 'published_date', 'stock_qty', 'price']),
            'slug' => \Illuminate\Support\Str::slug($r->input('title') . '-' . \Illuminate\Support\Str::random(10))
        ];
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

    public function update(Request $r, string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'author_id' => 'numeric|integer|exists:authors,id',
            'genre_id' => 'numeric|integer|exists:genres,id',
            'title' => 'string|max:255',
            'published_date' => 'string|date_format:Y-m-d',
            'stock_qty' => 'numeric|integer|min:0',
            'price' => 'numeric|min:0',
        ]);

        $data = [...$validated];

        \App\Models\Book::where('id', $id)->update($data);
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function updateCover(Request $r, string $id)
    {
        error_log('author_id is ' . $r->input('author_id'));
        $authorName = \App\Models\Author::where('id', $r->input('author_id'))->get()->first()->name;
        error_log('author name is' . $authorName);

        $coverName = null;

        $r->validate([
            'title' => 'required|string',
            'author_id' => 'required|integer|exists:authors,id',
            'cover_image' => 'image'
        ]);

        $cover = $r->file('cover_image');

        \Illuminate\Support\Facades\Storage::delete('public/covers/' . \App\Models\Book::find($id)->cover_image);

        $coverName = sprintf(
            '%s-%s-%s.%s',
            \Illuminate\Support\Str::kebab($authorName),
            \Illuminate\Support\Str::kebab($r->input('title')),
            \Illuminate\Support\Str::random(32),
            $cover->getClientOriginalExtension()
        );
        $cover->storePubliclyAs('covers', $coverName, 'public');

        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\Book::where('id', $id)->update([
            'cover_image' => $coverName
        ]);
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function deleteCover(string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\Book::where('id', $id)->update([
            'cover_image' => null
        ]);
        \Illuminate\Support\Facades\DB::commit();

        \Illuminate\Support\Facades\Storage::delete('public/covers/' . \App\Models\Book::find($id)->cover_image);

        return response()->noContent();
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
