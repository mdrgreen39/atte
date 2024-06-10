<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$date = Carbon::create(2024, 5, 28);
        //$date = Carbon::tomorrow();
        //Attendance::factory()->count(10)->create([
        //    'work_date' => $date,
        //]);

        $faker = FakerFactory::create();
        $userId = 13;

        $user = User::find($userId);

        if ($user) {
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i)->toDateString();

                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'start_work' => $faker->datetime(),
                    'end_work' => $faker->datetime(),
                    'total_break' => $faker->datetime(),
                    'total_work' => $faker->datetime(),
                ]);
            }
        } else {
            echo "User with ID {$userId} not found!";
        }

    }
}
