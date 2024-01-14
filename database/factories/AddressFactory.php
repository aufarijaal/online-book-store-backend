<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::where('is_admin', false)->get()->random()->id,
            'name' => 'Address',
            'full_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->city(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode()
        ];
    }
}
