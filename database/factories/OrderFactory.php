<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::where(['is_admin' => false])->get()->random()->id,
            'order_date' => fake()->dateTimeThisYear(),
            'total_amount' => 0,
            'paid' => true,
            'status' => 'paid'
        ];
    }


    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Order $order) {
            \App\Models\OrderItem::factory(fake()->numberBetween(1, 5))->create([
                'order_id' => $order->id
            ]);
        });
    }
}
