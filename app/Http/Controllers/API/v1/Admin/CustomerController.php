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

        $sortBy = $r->query('sortBy') ?? "id";
        $dataPerPage = $r->query('dataPerPage') ?? 20;
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $customersCount = \App\Models\User::where('is_admin', false)->count();;
        $customers = \App\Models\User::select(['id', 'name', 'email', 'email_verified_at', 'created_at'])->where('name', 'LIKE', '%' . $q . '%')
            ->where('is_admin', false)
            ->orderBy($sortBy, $sortDirection)
            ->paginate($dataPerPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $customers,
            'count' => $customersCount
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6'],
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

    public function update(Request $r, string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($id)],
            'set_as_verified' => 'required|boolean'
        ]);

        $customer = \App\Models\User::find($id);

        $customer->name = $validated['name'];
        $customer->email = $validated['email'];

        // if set as verified is true and current customer status is already verified, ignore
        if ((bool)$validated['set_as_verified'] && !is_null($customer->email_verified_at)) {
            $customer->email_verified_at = $customer->email_verified_at;

            // if set as verified is true but the current verified status is null, set the value to now()
        } else if ((bool)$validated['set_as_verified'] && is_null($customer->email_verified_at)) {
            $customer->email_verified_at = now();

            // if set as verified is false and current customer is verfied, set to null
        } else if (!((bool)$validated['set_as_verified'] && is_null($customer->email_verified_at))) {
            $customer->email_verified_at = null;
        }
        $customer->save();

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function resetPassword(Request $r, string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'password' => ['required', 'string', 'max:255', 'min:6']
        ]);

        $customer = \App\Models\User::find($id);

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
