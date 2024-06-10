<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $editPermission = Permission::firstOrCreate(['name' => 'edit']);

        $adminRole->givePermissionTo($editPermission);

        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole('user');

        $user = User::find(13);
        $user->removeRole('user');
        $user->assignRole('admin');
        $user->givePermissionTo($editPermission);

        }

    }
}
