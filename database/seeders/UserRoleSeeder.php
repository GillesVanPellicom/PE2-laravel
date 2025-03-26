<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\EmployeeContract;
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
    }
}
