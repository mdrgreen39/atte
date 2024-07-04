<?php

namespace Database\Factories;

use App\Models\User;
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
        $user = User::inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'work_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'start_work' => $this->faker->datetime(),
            'end_work' => $this->faker->datetime(),
            'total_break' => $this->faker->time(),
            'total_work' => $this->faker->time(),
        ];
    }
}
