<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organizer_id' => User::factory(),
            'city_id' => City::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'event_date_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'event_duration' => $this->faker->numberBetween(1, 8),
            'image' => $this->faker->imageUrl(640, 480, 'event', true),
            'location' => $this->faker->address,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
