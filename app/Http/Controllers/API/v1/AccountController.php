<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function getProfile(Request $r)
    {
        $profile = \App\Models\User::select(['id', 'name', 'email'])->where('id', auth()->user()->id)->get()->first();

        return response()->json([
            'message' => 'OK',
            'data' => $profile
        ]);
    }

    public function updateName(Request $r)
    {
        $validated = $r->validate([
            'name' => 'required|string|max:255'
        ]);

        \App\Models\User::where('id', auth()->user()->id)->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            'message' => 'OK',
        ]);
    }

    public function updateEmail(Request $r)
    {
        $validated = $r->validate([
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore(auth()->user()->id)]
        ]);

        \App\Models\User::where('id', auth()->user()->id)->update([
            'email' => $validated['email']
        ]);

        return response()->json([
            'message' => 'OK',
        ]);
    }

    public function updatePassword(Request $r)
    {
        $validated = $r->validate([
            'old_password' => 'required|string|max:255',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::where('id', auth()->user()->id)->get()->first();

        if (!\Illuminate\Support\Facades\Hash::check($validated['old_password'], $user->password)) {
            return response([
                'message' => 'Old password is invalid',
                'errors' => [
                    'old_password' => [
                        'Old Password is invalid'
                    ]
                ]
            ], 422);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
