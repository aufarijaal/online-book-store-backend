<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $orders = \App\Models\Order::with(['orderItems.book'])
            ->where('user_id', auth()->user()->id)->get();

        // if ($r->has('sortBy')) {
        //     if ($r->query('sortBy') === 'payment') {
        //         $orders->orderBy('paid', $r->query('order') ?? 'asc');
        //     } else if ($r->query('sortBy') === 'amount') {
        //         $orders->orderBy('total_amount', $r->query('order') ?? 'asc');
        //     }
        // }

        if (count($orders) > 0) {
            $orders->each(function ($item) {
                $item->orderItems->each(function ($orderItem) {
                    if (!is_null($orderItem->book->cover_image) && !str_starts_with($orderItem->book->cover_image, 'http')) {
                        $path = asset('storage/covers/' . $orderItem->book->cover_image);
                        $orderItem->book->cover_image = $path;
                    }
                });
            });
        }

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
