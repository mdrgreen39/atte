<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // "user" ロールの作成または取得
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // "edit" パーミッションの作成または取得
        $editPermission = Permission::firstOrCreate(['name' => 'edit']);
    }
}
