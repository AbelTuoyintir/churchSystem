<?php

namespace Database\Factories;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HouseholdMember>
 */
class HouseholdMemberFactory extends Factory
{
    protected $model = HouseholdMember::class;

    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'person_id' => Person::factory(),
            'role' => fake()->randomElement(['head', 'spouse', 'child', 'other']),
        ];
    }
}
