<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $sortBy = $r->query('sortBy') ?? "newest";
        $page = $r->query('page') ?? 1;

        $orders = \App\Models\Order::with(['orderItems.book'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('order_date', $sortBy === 'newest' ? 'desc' : 'asc')
            ->paginate(5, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $orders
        ]);
    }

    public function store(Request $r)
    {
        // error_log(json_encode($r->all()));
        \Illuminate\Support\Facades\DB::beginTransaction();
        $validated = $r->validate([
            'ids' => 'required|array',
            'ids.*' => 'numeric|integer|exists:cart_items,id'
        ]);

        $cartItems = \App\Models\CartItem::whereIn('id', $validated['ids'])->with(['book'])->get();
        $cartItems->each(function ($item) {
            if ($item->qty > $item->book->stock_qty) {
                return response([
                    'message' => 'Insufficient stock',
                    'errors' => [
                        'qty' => 'Insufficient stock'
                    ]
                ]);
            }
        });

        $order = \App\Models\Order::create([
            'user_id' => auth()->user()->id,
            'order_date' => now(),
            'total_amount' => $cartItems->sum('item_price'),
            'paid' => false
        ]);

        foreach ($cartItems as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $item->book->id,
                'qty' => $item->qty,
                'item_price' => $item->item_price
            ]);
        }

        // \App\Models\CartItem::whereIn($validated['ids'])->delete();

        \Illuminate\Support\Facades\DB::commit();
    }
}
