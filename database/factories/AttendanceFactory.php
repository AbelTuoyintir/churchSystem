<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'person_id' => null,
            'service_id' => null,
            'event_id' => null,
            'group_id' => null,
            'attended_on' => now()->toDateString(),
            'status' => 'present',
            'checked_in_at' => now(),
            'headcount' => 1,
            'recorded_by' => null,
            'notes' => null,
        ];
    }
}
