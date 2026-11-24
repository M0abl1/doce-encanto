<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_phone' => '7599'.fake()->numerify('#######'),
            'customer_email' => fake()->safeEmail(),
            'pickup_date' => now()->addDays(fake()->numberBetween(2, 20))->toDateString(),
            'notes' => null,
            'status' => OrderStatus::Novo,
            'total_cents' => 0,
        ];
    }
}
