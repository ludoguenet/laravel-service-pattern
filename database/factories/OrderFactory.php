<?php

namespace Database\Factories;

use App\Models\Depot;
use App\Models\User;
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
            'status' => fake()->randomElement(['ready', 'processing', 'completed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'scheduled_date' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'processed_at' => null,
            'depot_id' => Depot::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function ready()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ready',
                'scheduled_date' => now(),
            ];
        });
    }

    public function highPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }
}
