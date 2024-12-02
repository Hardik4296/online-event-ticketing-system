<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => 1,
            'ticket_type' => $this->faker->word,
            'price' => $this->faker->numberBetween(10, 100),
            'quantity' => $this->faker->numberBetween(1, 10),
            'available_quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
