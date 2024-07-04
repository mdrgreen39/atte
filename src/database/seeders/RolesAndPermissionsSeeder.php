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
        // "admin" ロールの作成または取得
        //$adminRole = Role::firstOrCreate(['name' => 'admin']);

        // "user" ロールの作成または取得
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // "edit" パーミッションの作成または取得
        $editPermission = Permission::firstOrCreate(['name' => 'edit']);

        // "admin" ロールに "edit" パーミッションを付与
        //$adminRole->givePermissionTo($editPermission);

        // "user" ロールに "edit" パーミッションを付与
        //$userRole->givePermissionTo($editPermission);

        // 必要に応じて "admin" ロールから "edit" パーミッションを削除
        // $adminRole->revokePermissionTo($editPermission);

        // 必要に応じて "user" ロールから "edit" パーミッションを削除
        //$userRole->revokePermissionTo($editPermission);

    }
}
