<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{

    protected $model = Attendance::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'work_date' => Carbon::today(),
            'start_work' => $this->faker->time(),
            'end_work' => $this->faker->time(),
            'total_break' => $this->faker->time(),
            'total_work' => $this->faker->time(),
        ];
    }
}
