<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketPurchase>
 */
class TicketPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_UID' => $this->faker->numberBetween(100000, 999999),
            'group_id' => $this->faker->numberBetween(100000, 999999),
            'user_id' => 1,
            'event_id' => 1,
            'ticket_id' => 1,
            'quantity' => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->numberBetween(1, 10),
            'payment_id' => $this->faker->numberBetween(1, 10000),
            'transaction_status' => 'success'
        ];
    }
}
