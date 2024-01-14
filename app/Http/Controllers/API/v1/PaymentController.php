<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    public function checkout(Request $r)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \Illuminate\Support\Facades\Validator::validate(['ids' => $r->query('ids')], [
            'ids' => 'required|array',
            'ids.*' => 'required|numeric|integer|exists:cart_items,id'
        ]);


        $cartItems = \App\Models\CartItem::with(['book'])
            ->where("user_id", auth()->user()->id)
            ->whereIn("id", $r->query('ids'))
            ->get();

        $orderItemsToCreate = collect();

        $cartItems->each(function ($item) use ($orderItemsToCreate) {
            if ($item->book->stock_qty < (int)$item->qty) {
                return response([
                    'message' => 'Insufficient stock',
                    'errors' => [
                        'qty' => [
                            'Quantity exceeds available stock'
                        ]
                    ]
                ], 422);
            }

            if (!is_null($item->book->cover_image)) {
                $item->book->cover_image = asset('storage/covers/' . $item->book->cover_image);
            }

            $orderItem = \App\Models\OrderItem::make([
                'book_id' => $item->book->id,
                'qty' => $item->qty,
                'item_price' => $item->item_price
            ]);

            $orderItemsToCreate->push($orderItem);
        });

        $address = \App\Models\Address::where('user_id', auth()->user()->id)->where('is_active', true)->get()->first();

        $newOrder = \App\Models\Order::create([
            'user_id' => auth()->user()->id,
            'order_date' => now(),
            'total_amount' => $cartItems->sum('item_price'),
            'paid' => false,
            'status' => 'unpaid',
            'address' => $address->full_address . " - " . $address->city . " - " . $address->state . " - " . $address->postal_code . " - " . $address->country
        ]);

        $orderItemsToCreate->each(function ($item) use ($newOrder) {
            $item->order_id = $newOrder->id;
            $item->save();
        });

        $cartItems->each(function ($item) use ($newOrder) {
            $item->order_id = $newOrder->id;
            $item->save();

            // Deprecated action
            $item->unavailable = $item->book->stock_qty < $item->qty;
        });


        $createdOrderItems = \App\Models\OrderItem::with(['book:id,price,title as name'])
            ->select(['id', 'qty as quantity', 'book_id'])
            ->where('order_id', $newOrder->id)->get()->toArray();

        foreach ($createdOrderItems as &$item) {
            $bookName = $item['book']['name'];
            $price = $item['book']['price'];
            unset($item['book']);
            $item['name'] = $bookName;
            $item['price'] = $price;
        }

        $params = [
            "transaction_details" => [
                "order_id" => $newOrder->id,
                "gross_amount" => $newOrder->total_amount,
            ],
            "credit_card" => ["secure" => true],
            "item_details" => $createdOrderItems,
            "customer_details" => [
                "first_name" => auth()->user()->name,
                "last_name" => "",
                "email" => auth()->user()->email,
                "phone" => "",
                "billing_address" => [
                    "first_name" => auth()->user()->name,
                    "last_name" => "",
                    "email" => auth()->user()->email,
                    "phone" => "",
                    "address" => $address->full_address,
                    "city" => $address->city,
                    "postal_code" => $address->postal_code,
                    "country_code" => $address->country,
                ],
                "shipping_address" => [
                    "first_name" => auth()->user()->name,
                    "last_name" => "",
                    "email" => auth()->user()->email,
                    "phone" => "",
                    "address" => $address->full_address,
                    "city" => $address->city,
                    "postal_code" => $address->postal_code,
                    "country_code" => $address->country,
                ],
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $newOrder->token = $snapToken;
        $newOrder->save();

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'message' => 'OK',
            'data' => [
                'cartItems' => $cartItems,
                'token' => $snapToken
            ]
        ]);
    }

    public function reorder(Request $r)
    {
        // 
    }

    public function callback(Request $r)
    {
        $serverKey = config('services.midtrans.serverKey');
        $hashed = hash('sha512', $r->order_id . $r->status_code . $r->gross_amount . $serverKey);
        if ($hashed == $r->signature_key) {
            error_log("Transaction Status :" . $r->transaction_status);
            if ($r->transaction_status == 'settlement') {
                \Illuminate\Support\Facades\DB::beginTransaction();

                $order = \App\Models\Order::find($r->order_id);
                $order->paid = true;
                $order->status = 'paid';
                $order->save();

                $cartItems = \App\Models\CartItem::with(['book'])->where('order_id', $r->order_id)->get();
                $cartItems->each(function ($item) {
                    // Reduce the stock of the successfully boughted cart items by their qty.
                    $item->book->stock_qty -= $item->qty;
                    $item->book->save();

                    // Delete the boughted cart items.
                    $item->delete();
                });

                \Illuminate\Support\Facades\DB::commit();
            } else if ($r->transaction_status == 'expire') {
                \Illuminate\Support\Facades\DB::beginTransaction();

                $order = \App\Models\Order::find($r->order_id);
                $order->status = 'expired';
                $order->save();

                \Illuminate\Support\Facades\DB::commit();
            }
        }
    }
}
