<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserRolesAndPermissionsSeeder::class,
        ]);

        if (App::environment('local')) {
            $this->call([
                //UserSeeder::class,
                //AttendancesTableSeeder::class,
                RolesAndPermissionsSeeder::class,
                //AdminUserSeeder::class,
                //UserRolesSeeder::class,

            ]);
        }


    }
}
