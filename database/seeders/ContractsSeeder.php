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
use App\Helpers\ConsoleHelper;

class ContractsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contracts')->insert([
            ['employee_id' => 1, 'job_id' => 6, 'location_id' => 8, 'start_date' => '2024-03-01', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 2, 'job_id' => 1, 'location_id' => 10, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 4, 'job_id' => 8, 'location_id' => 8, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 5, 'job_id' => 10, 'location_id' => 1, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            /*//Distribution Center Manager Szymon
             * ['employee_id' => 9, 'job_id' => 9, 'location_id' => 2, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],*/]);

        $directoryPath = public_path('contracts');
        exec("rm -rf " . escapeshellarg($directoryPath), $output, $status);
        Log::info($status === 0 ? "Directory removed" : "Failed to remove directory");

        // Ensure one employee cannot have two contracts at the same time
        $contractUserIds = \App\Models\Employee::doesntHave('contracts')->pluck('id')->shuffle()->take(200);
        foreach ($contractUserIds as $employeeId) {
            \Database\Factories\ContractFactory::new()->create(['employee_id' => $employeeId]);
        }

        $contracts = EmployeeContract::all();
        foreach ($contracts as $contract) {
            $contractStartTime = microtime(true);
            ConsoleHelper::task(str_pad("[$contract->contract_id]", 7, ' ', STR_PAD_RIGHT)." Contract for: ".$contract->employee->user->first_name . ' ' .$contract->employee->user->last_name,
                function () use ($contract) {
                    EmployeeController::generateEmployeeContract($contract->contract_id);
                });
            $executionTimes[] = (microtime(true) - $contractStartTime) * 1000; // Convert to milliseconds
        }

    }
}
