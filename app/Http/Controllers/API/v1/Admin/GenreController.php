<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forDropdown')) {
            $genres = \App\Models\Genre::get(['id', 'name']);

            return response()->json([
                'message' => 'OK',
                'data' => $genres
            ]);
        }

        $sizePerPage = $r->query('sizePerPage') ?? 20;
        $genres = $r->query('q') ?
            \App\Models\Genre::where('name', 'LIKE', '%' . $r->query('q') . '%')->paginate($sizePerPage) :
            \App\Models\Genre::paginate($sizePerPage);

        return response()->json([
            'message' => 'OK',
            'data' => $genres
        ]);
    }

    public function edit(string $id)
    {
        $genre = \App\Models\Genre::find($id, ['id', 'name', 'description']);

        return response()->json([
            'message' => 'OK',
            'data' => $genre
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $r->validate([
            'name' => 'required|string|max:255|unique:genres,name',
            'description' => 'required|string|max:255',
        ]);

        \App\Models\Genre::create(
            [
                ...$r->only(['name', 'description']),
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
            'id' => 'required|numeric|integer|exists:genres,id',
            'name' => 'string|max:255',
            'description' => 'string|max:255',
        ]);

        \App\Models\Genre::where('id', $validated['id'])->update([
            ...$r->only(['name', 'description']),
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
        \App\Models\Genre::whereIn('id', explode(',', $r->query('ids')))->delete();
        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}
