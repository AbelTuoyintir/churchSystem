<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' Group',
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['small_group', 'ministry', 'committee', 'class']),
            'leader_id' => null,
            'meeting_day' => fake()->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'meeting_time' => '19:00',
            'location' => fake()->city(),
            'is_active' => true,
        ];
    }
}
