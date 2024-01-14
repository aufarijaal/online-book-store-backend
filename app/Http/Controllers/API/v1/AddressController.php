<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $r)
    {
        $addresses = $r->has("forDropdown") ?
            \App\Models\Address::select(['id', 'name', 'full_address', 'city', 'state', 'country', 'postal_code', 'is_active'])->where('user_id', auth()->user()->id)->get() :
            \App\Models\Address::where('user_id', auth()->user()->id)->get();

        return response()->json([
            'message' => 'OK',
            'data' => $addresses
        ]);
    }

    public function store(Request $r)
    {
        $validated = $r->validate([
            'name' => 'required|string',
            'full_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        $createdAddress = \App\Models\Address::create([
            'user_id' => auth()->user()->id,
            ...$validated
        ]);

        if ((bool)$validated['is_active']) {
            $addressesToDeactivate = \App\Models\Address::where('user_id', auth()->user()->id)->whereNot('id', $createdAddress->id)->get();

            $addressesToDeactivate->each(function ($each) {
                $each->is_active = false;
                $each->save();
            });
        }

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function update(Request $r, string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        \Illuminate\Support\Facades\Validator::validate(['id' => (int)$id, ...$r->all()], [
            'id' => 'required|numeric|integer|exists:addresses,id',
            'name' => 'required|string',
            'full_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        $updatedAddress = \App\Models\Address::where('id', $id)
            ->update($r->only(['name', 'full_address', 'city', 'state', 'country', 'postal_code', 'is_active']));

        if ((bool)$r->input('is_active')) {
            $addressesToDeactivate = \App\Models\Address::where('user_id', auth()->user()->id)->whereNot('id', $id)->get();

            $addressesToDeactivate->each(function ($each) {
                $each->is_active = false;
                $each->save();
            });
        }

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function destroy(string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $addressToDelete = \App\Models\Address::where('user_id', auth()->user()->id)->where('id', $id)->get()->first();

        if ($addressToDelete->is_active) {
            $firstAddressToBeActivated = \App\Models\Address::where('user_id', auth()->user()->id)->whereNot('id', $id)->get()->first();
            $firstAddressToBeActivated->is_active = true;
            $firstAddressToBeActivated->save();
        }

        $addressToDelete->delete();

        \Illuminate\Support\Facades\DB::commit();

        return response()->noContent();
    }
}
