<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $sortBy = $r->query('sortBy') ?? "id";
        $dataPerPage = $r->query('dataPerPage') ?? 20;
        $page = $r->query('page') ?? 1;
        $sortDirection = $r->query('sortDirection') ?? 'asc';
        $q = $r->query('q') ?? '';

        $orders = \App\Models\Order::count();

        $orders = \App\Models\Order::select([
            'orders.id',
            'orders.user_id as customer_id',
            'users.name as customer_name',
            'orders.order_date',
            'orders.total_amount',
            'orders.address',
            'orders.paid',
        ])->where('users.name', 'LIKE', '%' . $q . '%')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($dataPerPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'OK',
            'data' => $orders,
            'count' => $orders
        ]);
    }

    public function show(string $id)
    {
        $orderDetail = \App\Models\Order::with([
            'orderItems.book',
            'customer:id,name'
        ])->find($id);

        $orderDetail->orderItems->each(function ($orderItem) {
            if (!is_null($orderItem->book->cover_image)) {
                $orderItem->book->cover_image = asset('storage/covers/' . $orderItem->book->cover_image);
            }
        });

        return response()->json([
            'message' => 'OK',
            'data' => $orderDetail
        ]);
    }

    public function store(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $validated = $r->validate([
            'user_id' => 'required|numeric|integer|exists:users,id',
            'order_date' => 'required|string|max:255|date_format:Y-m-d H:i:s',
            'total_amount' => 'required|numeric',

            'order_items' => 'required|array',

            'order_items.*.book.id' => 'required|numeric|integer|exists:books,id',
            'order_items.*.qty' => 'required|numeric|integer|min:1',
            'order_items.*.item_price' => 'required|numeric'
        ]);

        $orderItemsToInsert = [];

        foreach ($validated['order_items'] as $orderItem) {
            $book = \App\Models\Book::where('id', $orderItem['book']['id'])->get()->first();

            if ((int)$orderItem['qty'] > (int)$book->stock_qty) {
                return response([
                    'message' => 'the qty field is insufficient with the available stock',
                    'errors' => [
                        'qty' => [
                            sprintf("the qty field of book %s is insufficient with the available stock", $book->title)
                        ]
                    ]
                ], 422);
            } else {
                array_push($orderItemsToInsert, [
                    'book_id' => $book->id,
                    'qty' => $orderItem['qty'],
                    'item_price' => $orderItem['item_price']
                ]);
            }
        }

        $order = \App\Models\Order::create([
            'user_id' => $validated['user_id'],
            'order_date' => now(),
            'total_amount' => collect($validated['order_items'])->sum('item_price')
        ]);

        for ($i = 0; $i < count($orderItemsToInsert); $i++) {
            \App\Models\OrderItem::create([
                ...$orderItemsToInsert[$i],
                'order_id' => $order->id
            ]);
        }

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK'
        ]);
    }
}
