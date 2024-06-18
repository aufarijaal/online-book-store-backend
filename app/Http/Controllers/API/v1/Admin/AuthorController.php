<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forDropdown')) {
            $authors = \App\Models\Author::orderBy('name')->get(['id', 'name', 'nationality']);

            return response()->json([
                'message' => 'OK',
                'data' => $authors
            ]);
        }

        $sortBy = $r->query('sortBy') ?? "id";
        $dataPerPage = $r->query('dataPerPage') ?? 20;
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $authorsCount = \App\Models\Author::count();
        $authors = \App\Models\Author::select([
            'authors.id',
            'authors.name',
            'authors.dob',
            'authors.nationality',
            \Illuminate\Support\Facades\DB::raw('COUNT(books.id) as books_count')
        ])->where(function ($query) use ($q) {
            $query
                ->where('name', 'LIKE', '%' . $q . '%')
                ->orWhere('nationality', 'LIKE', '%' . $q . '%');
        })
            ->leftJoin('books', 'books.author_id', '=', 'authors.id')
            ->groupBy('authors.id', 'authors.name', 'authors.dob', 'authors.nationality')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($dataPerPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $authors,
            'count' => $authorsCount
        ]);
    }

    public function edit(string $id)
    {
        $author = \App\Models\Author::find($id, ['id', 'name', 'dob', 'nationality']);

        return response()->json([
            'message' => 'OK',
            'data' => $author
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $r->validate([
            'name' => 'required|string|max:255|unique:authors,name',
            'dob' => 'required|string|max:255|date_format:Y-m-d',
            'nationality' => 'required|string|max:255',
        ]);

        \App\Models\Author::create(
            [
                ...$r->only(['name', 'dob', 'nationality']),
                'slug' => \Illuminate\Support\Str::slug($r->input('name'))
            ]
        );
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function update(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'id' => 'required|numeric|integer|exists:authors,id',
            'name' => ['string', 'max:255', \Illuminate\Validation\Rule::unique('authors', 'name')->ignore($r->input('id'))],
            'dob' => 'string|max:255|date_format:Y-m-d',
            'nationality' => 'string|max:255',
        ]);

        \App\Models\Author::where('id', $validated['id'])->update([
            ...$r->only(['name', 'dob', 'nationality']),
            'slug' => \Illuminate\Support\Str::slug($r->input('name'))
        ]);
        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function destroy(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\Author::whereIn('id', explode(',', $r->query('ids')))->delete();
        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}
