<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $editPermission = Permission::where('name', 'edit')->first();

        //管理者にするIDをfind()のカッコ内に入力
        $user = User::find(13);
        $user->syncRoles([$adminRole]);
        $user->givePermissionTo($editPermission);

    }
}
