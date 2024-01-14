<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $r)
    {
        if ($r->has('forDropdown')) {
            $customers = \App\Models\User::where('is_admin', false)->get(['id', 'name']);

            return response()->json([
                'message' => 'OK',
                'data' => $customers
            ]);
        }

        $sizePerPage = $r->query('sizePerPage') ?? 20;
        $customers = $r->query('q') ?
            \App\Models\User::where('name', 'LIKE', '%' . $r->query('q') . '%')->where('is_admin', false)->paginate($sizePerPage) :
            \App\Models\User::where('is_admin', false)->paginate($sizePerPage);

        return response()->json([
            'message' => 'OK',
            'data' => $customers
        ]);
    }

    public function edit(string $id)
    {
        $customer = \App\Models\User::find($id, ['id', 'name', 'email', 'email_verified_at']);

        return response()->json([
            'message' => 'OK',
            'data' => $customer
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email_verified_at' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
            'set_as_verified' => 'required|boolean'
        ]);

        \App\Models\User::insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => (bool)$validated['set_as_verified'] ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function update(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'id' => ['required', 'numeric', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($r->input('id'))],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'set_as_verified' => 'required|boolean'
        ]);

        $customer = \App\Models\User::find($validated['id']);

        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        $customer->email_verified_at = (bool)$validated['set_as_verified'] ? now() : null;
        $customer->save();

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function changePassword(Request $r, string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = \Illuminate\Support\Facades\Validator::validate([
            'id' => $id,
            'password' => $r->input('password')
        ], [
            'id' => ['required', 'numeric', 'integer'],
            'password' => ['required', 'string', 'max:255', \Illuminate\Validation\Rules\Password::defaults()]
        ]);

        $customer = \App\Models\User::find($validated['id']);

        $customer->password = $validated['password'];
        $customer->save();

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function destroy(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\User::whereIn('id', explode(',', $r->query('ids')))->delete();
        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}
