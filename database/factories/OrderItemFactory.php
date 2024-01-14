<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $book = \App\Models\Book::all()->random();
        $qty = fake()->numberBetween(1, $book->stock_qty);

        return [
            'order_id' => \App\Models\Order::all()->random()->id,
            'book_id' => $book->id,
            'qty' => $qty,
            'item_price' => $qty * $book->price,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\OrderItem $orderItem) {
            $order = \App\Models\Order::find($orderItem->order_id);
            $currentTotalAmount = (float)$order->total_amount;

            $order->total_amount = $currentTotalAmount + $orderItem->item_price;
            $order->save();
        });
    }
}
