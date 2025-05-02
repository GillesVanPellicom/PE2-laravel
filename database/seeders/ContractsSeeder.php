<?php

namespace Database\Seeders;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Country, City, Address, EmployeeContract, User, EmployeeFunction, Team, Role, Location};
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\Validate_Adult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ContractsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contracts')->insert([
            ['employee_id' => 1, 'job_id' => 6, 'location_id' => 8, 'start_date' => '2024-03-01', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 2, 'job_id' => 1, 'location_id' => 10, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 4, 'job_id' => 8, 'location_id' => 8, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 5, 'job_id' => 9, 'location_id' => 1, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        $directoryPath = public_path('contracts');
        exec("rm -rf " . escapeshellarg($directoryPath), $output, $status);
        Log::info($status === 0 ? "Directory removed" : "Failed to remove directory");

        $contracts = EmployeeContract::all();
        foreach($contracts as $contract) {
            EmployeeController::generateEmployeeContract($contract->contract_id);
        }

        
    }
}
