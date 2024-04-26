<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $r)
    {
        $cartItems = \App\Models\CartItem::with(['book'])->where("user_id", auth()->user()->id)->get();

        if (count($cartItems) > 0) {
            $cartItems->each(function ($item) {
                if (!is_null($item->book->cover_image)) {
                    $item->book->cover_image = asset('storage/covers/' . $item->book->cover_image);
                }
                // Deprecated action
                $item->unavailable = $item->book->stock_qty < $item->qty;
            });
        }

        return response()->json([
            'message' => 'OK',
            'data' => $cartItems
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        $validated = $r->validate([
            'book_id' => 'required|numeric|integer|exists:books,id',
            'qty' => 'required|numeric|integer',
            'item_price' => 'required|numeric',
        ]);

        $addresses = \App\Models\Address::where('user_id', auth()->user()->id)->where('is_active', 1)->get();

        if (count($addresses) === 0) {
            return response([
                'message' => 'Please add at least one active address in your settings',
                'errors' => [
                    'address' => [
                        'Please add at least one active address in your settings'
                    ]
                ]
            ], 422);
        }

        $stockRemaining = \App\Models\Book::select(['id', 'stock_qty'])->where('id', $validated['book_id'])->get()->first();

        // Check stock availability
        if ($stockRemaining->stock_qty < (int)$validated['qty']) {
            return response([
                'message' => 'Insufficient stock',
                'errors' => [
                    'qty' => [
                        'Quantity exceeds available stock'
                    ]
                ]
            ], 422);
        }

        // Check duplicate book
        $cartItemDuplicate = \App\Models\CartItem::where('book_id', (int)$validated['book_id'])->with(['book'])->get()->first();
        if ($cartItemDuplicate) {
            $cartItemDuplicate->qty += (int)$validated['qty'];
            $cartItemDuplicate->item_price = (int)$cartItemDuplicate->qty * $cartItemDuplicate->book->price;
            $cartItemDuplicate->save();
        } else {
            \App\Models\CartItem::create([
                'user_id' => auth()->user()->id,
                ...$validated
            ]);
        }


        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK',
        ]);
    }

    public function destroy(string $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        \App\Models\CartItem::where('id', $id)->delete();

        \Illuminate\Support\Facades\DB::commit();
    }

    public function count()
    {
        $count = \App\Models\CartItem::where("user_id", auth()->user()->id)->count();

        return response()->json([
            'message' => 'OK',
            'data' => [
                'count' => $count
            ]
        ]);
    }
}
