<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\EloquentFactories\Factory<\App\Models\Event>
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
        $title = $this->faker->sentence(3);
        $capacity = $this->faker->numberBetween(20, 200);
        $available = $this->faker->numberBetween(0, $capacity);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(4),
            'category' => Arr::random(['conference', 'workshop', 'webinar', 'meetup', 'community']),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'start_date' => $this->faker->dateTimeBetween('+1 days', '+1 year'),
            'capacity' => $capacity,
            'available_seats' => $available,
            'ticket_price' => $this->faker->numberBetween(0, 250000),
            'status' => Arr::random(['scheduled', 'cancelled', 'completed']),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
