<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forDropdown')) {
            $authors = \App\Models\Author::get(['id', 'name', 'nationality']);

            return response()->json([
                'message' => 'OK',
                'data' => $authors
            ]);
        }

        $sizePerPage = $r->query('sizePerPage') ?? 20;
        $authors = $r->query('q') ?
            \App\Models\Author::where('name', 'LIKE', '%' . $r->query('q') . '%')->paginate($sizePerPage) :
            \App\Models\Author::paginate($sizePerPage);

        return response()->json([
            'message' => 'OK',
            'data' => $authors
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
            'name' => 'string|max:255|unique:authors,name',
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
