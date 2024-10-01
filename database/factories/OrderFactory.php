<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(OrderStatus::cases())->value,
            'delivery_address' => fake()->address(),
            'delivery_time' => fake()->dateTimeBetween('now', '+2 hours'),
            'total_price' => fake()->numberBetween(100, 10000),
            'user_id' => User::inRandomOrder()->value('id'),
        ];
    }
}
