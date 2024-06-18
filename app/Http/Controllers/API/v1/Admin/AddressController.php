<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $r)
    {
        $sortBy = $r->query('sortBy') ?? "id";
        $dataPerPage = $r->query('dataPerPage') ?? 20;
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $addressesCount = \App\Models\Address::count();
        $addresses = \App\Models\Address::select(['addresses.*', 'users.name as customer_name'])
            ->where(function ($query) use ($q) {
                $query
                    ->where('addresses.name', 'LIKE', '%' . $q . '%')
                    ->orWhere('addresses.full_address', 'LIKE', '%' . $q . '%')
                    ->orWhere('addresses.city', 'LIKE', '%' . $q . '%')
                    ->orWhere('addresses.state', 'LIKE', '%' . $q . '%')
                    ->orWhere('addresses.country', 'LIKE', '%' . $q . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $q . '%')
                    ->orWhere('addresses.postal_code', 'LIKE', '%' . $q . '%');
            })
            ->join('users', 'addresses.user_id', '=', 'users.id')
            ->orderBy($sortBy == 'customer_name' ? 'users.name' : $sortBy, $sortDirection)
            ->paginate($dataPerPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $addresses,
            'count' => $addressesCount
        ]);
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            "user_id" => "required|numeric|integer|exists:users,id",
            "name" => "required|string|max:255",
            "full_address" => "required|string",
            "city" => "required|string|max:255",
            "state" => "required|string|max:255",
            "country" => "required|string|max:255",
            "postal_code" => "required|string|max:255",
            "is_active" => "required|boolean",
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        $addressesByThisCustomer = \App\Models\Address::where('user_id', $v['user_id'])->get();

        $addressesByThisCustomer->each(function ($address) use ($v) {
            if ((bool)$v['is_active'] && $address->is_active) {
                $address->is_active = false;
                $address->save();
            }
        });

        \App\Models\Address::create($v);

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function edit(string $id)
    {
        $address = \App\Models\Address::find($id, ['id', 'user_id', 'name', 'full_address', 'city', 'state', 'country', 'postal_code', 'is_active']);

        return response()->json([
            'message' => 'OK',
            'data' => $address
        ]);
    }

    public function update(Request $r)
    {
        $r->validate([
            "id" => 'required|numeric|integer|exists:addresses,id',
            "user_id" => "numeric|integer|exists:users,id",
            "name" => "string|max:255",
            "full_address" => "string",
            "city" => "string|max:255",
            "state" => "string|max:255",
            "country" => "string|max:255",
            "postal_code" => "string|max:255",
            "is_active" => "boolean",
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\Address::where('user_id', $r->input('user_id'))
            ->where('id', $r->input('id'))
            ->update($r->only(['name', 'full_address', 'city', 'state', 'country', 'postal_code', 'is_active']));

        \App\Models\Address::where('user_id', $r->input('user_id'))
            ->whereNot('id', $r->input('id'))
            ->get()
            ->each(function ($address) use ($r) {
                if ((bool)$r->input('is_active')) {
                    $address->is_active = false;
                    $address->save();
                }
            });

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function destroy(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \App\Models\Address::whereIn('id', explode(',', $r->query('ids')))->delete();
        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}