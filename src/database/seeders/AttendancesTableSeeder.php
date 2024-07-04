<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
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
        //全てのユーザーのattendanceデータを作成
        //日付指定
        //$date = Carbon::create(2024, 5, 28);

        //today or tomorrowの場合
        //$date = Carbon::tomorrow();

        //Attendance::factory()->count(10)->create([
        //    'work_date' => $date,
        //]);


        //特定のユーザーのattendanceデータを作成
        $faker = FakerFactory::create();
        //IDを指定
        $userId = 13;

        $user = User::find($userId);

        //特定のユーザーの30日分ののデータ作成
        if ($user) {
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i)->toDateString();

                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'start_work' => $faker->datetime(),
                    'end_work' => $faker->datetime(),
                    'total_break' => $faker->time(),
                    'total_work' => $faker->time(),
                ]);
            }
        } else {
            echo "User with ID {$userId} not found!";
        }

    }
}
