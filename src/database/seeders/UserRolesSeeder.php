<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRole = Role::where('name', 'user')->first();

        $users = User::all();
        foreach ($users as $user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole($userRole);
            }
        }
    }
}
