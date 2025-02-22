<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\ConsoleHelper;
use Spatie\Permission\PermissionRegistrar;


class RolesAndPermissionsSeeder extends Seeder {

  // ╔════════════════════════════════════════╗
  // ║              Definitions               ║
  // ╚════════════════════════════════════════╝

  private array $permissions = [
    // Post permissions
    'posts.edit.all',
    'posts.edit.own',

    // Console permissions
    'console.view',
    'console.cmd.reboot',
  ];


  private array $roles = [
    // Distribution center roles
    'sysadmin' => ['*'],
    'admin' => ['posts.edit.all', 'console.view', 'console.cmd.reboot'],
    'user' => ['posts.edit.own'],
  ];


  private array $roleInheritance = [
    'user' => 'admin',
  ];


  // DO NOT EDIT BELOW THIS POINT (r0997008, @Gilles)

  /**
   * Seed logic for the permission system.
   */
  public function run(): void {
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
          foreach ($this->roleInheritance as $parentRole => $childRole) {
            $childRoleInstance = Role::findByName($childRole);
            $parentRoleInstance = Role::findByName($parentRole);

            $parentPermissions = $parentRoleInstance->permissions()->pluck('name')->toArray();
            $childRoleInstance->givePermissionTo($parentPermissions);
          }
        });

        ConsoleHelper::success('Roles and permissions initialized');
      });

    } catch (Exception $e) {
      $errMsg = sprintf(
        "Permission system initialization failed with the following exception: %s in %s.",
        $e->getMessage(),
        $e->getFile(),
      );

      ConsoleHelper::error($errMsg);
      ConsoleHelper::success('Roles and permissions rollback completed');
    }
  }
}