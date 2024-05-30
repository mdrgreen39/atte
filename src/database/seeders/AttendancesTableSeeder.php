<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::create(2024, 5, 28);
        //$date = Carbon::tomorrow();
        Attendance::factory()->count(10)->create([
            'work_date' => $date,
        ]);
    }
}
