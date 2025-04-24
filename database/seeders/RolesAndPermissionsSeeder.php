<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\ConsoleHelper;
use Spatie\Permission\PermissionRegistrar;


class RolesAndPermissionsSeeder extends Seeder
{

    // ╔════════════════════════════════════════╗
    // ║              Definitions               ║
    // ╚════════════════════════════════════════╝

    private array $permissions = [
        'employee',
        'scan',
        "courier.route",
        "scan.deliver",

        // START API
        "token.create",
        // END API

        /* START Employees */
        "courier.packages",
        "HR.checkall",
        "HR.create",
        "HR.assign",
        /* END Employees */

        /*START Airport*/
        "airport.view",
        /*END Airport*/
        /* START Pickup */
        'pickup.view',
        'pickup.edit',
        /* END Pickup */
    ];


    private array $roles = [
        /* ADMIN */
        "admin" => ["*"],
        /* ADMIN */

        "employee" => ["employee"],

        /* START Courier */
        "scan" => ["scan"],
        "courier" => ["courier.route", "scan.deliver", "courier.packages"],
        /* END Courier */

        /* START Employees */
        "HRManager" => ["HR.create", "HR.assign"],
        "HR" => ["HR.checkall"],
        /* END Employees */

        /* START Airport */
        "airport" => ["airport.view"],
        /* END Airport */
        /* START Pickup */
        "pickup" => ["pickup.view", "pickup.edit"],
        /* END Pickup */

        // START API
        "api" => ["token.create"]
        //END API
    ];


    private array $roleInheritance = [
        /* START BASE -=- Everthing should in some way inherit from employee */
        "employee" => ["scan", "HR","pickup"],
        /* END BASE */

        /* START Courier */
        "scan" => ["courier"],
        /* END Courier */

        /* START Employees */
        "HR" => ["HRManager"],
        /* END Employees */
    ];


    // DO NOT EDIT BELOW THIS POINT (r0997008, @Gilles)

    /**
     * Seed logic for the permission system.
     */
    public function run(): void
    {
        ConsoleHelper::info('Initializing roles and permissions');
        ConsoleHelper::info('Starting transaction');
        try {
            // Place all logic in a transaction to ensure atomicity
            DB::transaction(function () {

                // Remove all existing roles and permissions
                ConsoleHelper::task('Removing potential existing roles and permissions', function () {
                    Role::query()->delete();
                    Permission::query()->delete();
                });


                // Reset permission cache
                ConsoleHelper::task('Resetting cached roles and permissions', function () {
                    app()[PermissionRegistrar::class]->forgetCachedPermissions();
                });


                // Define permissions
                ConsoleHelper::task('Defining permission nodes', function () {
                    foreach ($this->permissions as $permission) {
                        Permission::findOrCreate($permission);
                    }
                });


                // Update cache to know about the newly created permissions
                ConsoleHelper::task('Updating cache with new permission nodes', function () {
                    app()[PermissionRegistrar::class]->forgetCachedPermissions();
                });


                // Define roles and their permissions
                ConsoleHelper::task('Defining roles and assigning permissions', function () {
                    foreach ($this->roles as $roleName => $rolePermissions) {
                        $role = Role::findOrCreate($roleName);
                        if (in_array('*', $rolePermissions)) {
                            $role->givePermissionTo(Permission::all());
                        } else {
                            $role->givePermissionTo($rolePermissions);
                        }
                    }
                });


                // Apply inheritance
                ConsoleHelper::task('Applying role inheritance', function () {
                    foreach ($this->roleInheritance as $parentRole => $childRoles) {
                        $parentRoleInstance = Role::findByName($parentRole);
                        foreach ($childRoles as $childRole) {
                            $childRoleInstance = Role::findByName($childRole);
                            $parentPermissions = $parentRoleInstance->permissions()->pluck('name')->toArray();
                            $childRoleInstance->givePermissionTo($parentPermissions);
                        }
                    }
                });

                ConsoleHelper::success('Roles and permissions initialized');
            });

        } catch (Exception $e) {
            ConsoleHelper::printError($e);
            ConsoleHelper::success('Roles and permissions rollback completed');
        }
    }
}
