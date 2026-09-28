<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true) . ' Event',
            'description' => $this->faker->sentence(),
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'all_day' => false,
            'location' => $this->faker->streetAddress(),
            'category' => 'service',
            'is_public' => true,
        ];
    }
}
