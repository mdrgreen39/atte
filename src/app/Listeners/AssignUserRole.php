<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignUserRole
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;

        $userRole = Role::firstOrCreate(['name' => 'user']);

        $user->assignRole($userRole);

        $editPermission = Permission::firstOrCreate(['name' => 'edit']);

        $user->givePermissionTo($editPermission);
    }
}
