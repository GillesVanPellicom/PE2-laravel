<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\EmployeeContract;
use App\Models\Role;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $contracts = EmployeeContract::all();
        foreach($contracts as $contract){
            $user = $contract->employee->user;
            $role = $contract->function->role;
            if (empty($role)) continue;
            $user->assignRole($role);
        }

        $admin = User::find(1);
        $admin->assignRole('admin');
        $pickupUser = User::find(5); // Change to the correct user ID
        $pickupUser->assignRole('pickup');
        $role = Role::where('name','pickup')->first();
        $pickupUser->syncPermissions($role->permissions);

        $courierUser = User::find(6); // Change to the correct user ID
        $courierUser->assignRole('courier');
        $role = Role::where('name','courier')->first();
        $courierUser->syncPermissions($role->permissions);
        $courierUser->assignRole('scan');
        $role = Role::where('name','scan')->first();
        $courierUser->syncPermissions($role->permissions);

        $DcUser = User::find(9); // Change to the correct user ID
        $DcUser->assignRole('DCManager');
        $role = Role::where('name','DCManager')->first();
        $DcUser->syncPermissions($role->permissions);
    }
}
