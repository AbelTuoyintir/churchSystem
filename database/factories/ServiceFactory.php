<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) . ' Service',
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '09:00:00',
            'location' => $this->faker->streetAddress(),
            'is_active' => true,
        ];
    }
}
