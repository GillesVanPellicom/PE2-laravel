<?php


use App\Http\Middleware\Authenticate;
use Aws\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use Pnlinh\GoogleDistance\Facades\GoogleDistance;

use App\Http\Controllers\ChartController;
use App\Http\Controllers\PackageListController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\TrackPackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\contractController;
use App\Http\Controllers\flightscontroller;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\airportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DispatcherController;

// ======================= Start Authentication ====================== //



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Login
Route::get('/login', function () {
    return view('auth.login');
})->middleware("guest")->name('auth.login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    return app(AuthController::class)->authenticate($request, "customers");
})->name('auth.authenticate');

// Register
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'store'])->name('auth.store');

// Update
Route::post('/update', [AuthController::class, 'update'])->name('auth.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Customers
Route::middleware("auth")->group(function () {
    Route::get('/customers', [AuthController::class, 'showCustomers'])->name('customers');
});

// ======================= End Authentication ====================== //

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Login
Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('auth.authenticate');

// Register
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'store'])->name('auth.store');

// Update
Route::post('/update', [AuthController::class, 'update'])->name('auth.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Customers
Route::get('/customers', function () {
    if (!Auth::check()) {
        return redirect()->route('auth.login');
    }
    return view('customers');
})->name('customers');
Route::get('/customers', [AuthController::class, 'showCustomers'])->name('customers');

// ======================= Start Courier ====================== //

# => Courier Mobile app
use App\Http\Controllers\CourierRouteController;

Route::get('/courier', [CourierController::class, "index"])->middleware(["guest"])->name('courier');

Route::post('/courier', function (\Illuminate\Http\Request $request) {
    return app(AuthController::class)->authenticate($request, "courier.scan");
})->name('courier.authenticate');

Route::middleware("auth")->group(function () {
    Route::get('/courier/route', [CourierRouteController::class, 'showRoute'])
        ->middleware("permission:courier.route")->name('courier.route');

    Route::get('/courier/packages', [CourierController::class, "packages"])
        ->middleware("permission:courier.packages")->name('courier.packages');

    Route::get("/courier/scan", [CourierController::class, "scan"])
        ->middleware("permission:scan")->name("courier.scan");

    Route::get("/courier/getlastpackages", [CourierController::class, "getLastPackages"])
        ->middleware("permission:scan")->name("courier.lastPackages");

    Route::post("/courier/scanQr", [CourierController::class, "scanQr"])
        ->middleware("permission:scan")->name("courier.scanQr");
    
    Route::post("/courier/deliver/{id}", [TrackPackageController::class, "deliverPackage"])
        ->middleware("permission:scan.deliver")->name("courier.deliver");

    Route::get('/courier/logout', [AuthController::class, "logout"])
        ->middleware("permission:scan")->name("courier.logout");
}); 

# Test Route
Route::get("/courier/generate/{id}", [PackageController::class, "generateQRcode"])->name("generateQR");

//Route::get('/courier/route', [CourierRouteController::class, 'showRoute'])->name('courier.route');

Route::get('/distribution-center/{id}/packages', [CourierRouteController::class, 'getDistributionCenterPackages'])->name('distribution-center.packages');

# <= END Courier Mobile App

// Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');

// ======================= End Courier ====================== //

// ======================= Start Distribution ====================== //

Route::get('/packagechart', [ChartController::class, 'getPackageData'])->name('package.chart');

Route::get('/packagelist', [PackageListController::class, 'index'])->name('package.list');

// ======================= End Distribution ====================== //

// ======================= Start Employee ====================== //

Route::middleware(['permission:employee'])->group(function () {
    Route::post('/save-vacation', [VacationController::class, 'store'])->name('vacation.store');
    Route::get('/approved-vacations', [VacationController::class, 'getApprovedVacations']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/employees/calendar', [NotificationController::class, 'showCalendar'])->name('employees.calendar');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');
});

Route::middleware(['permission:HR.create'])->group(function(){
    Route::get('/manager-calendar', [EmployeeController::class, 'managerCalendar'])->name('manager.calendar');

    Route::get('/pending-vacations', [VacationController::class, 'getPendingVacations']);

    Route::post('/vacations/{id}/update-status', [VacationController::class, 'updateStatus']);

    Route::get('/employees/holiday-requests', [VacationController::class, 'showAllVacations'])->name('employees.holiday_requests');
});

Route::middleware(['permission:HR.checkall'])->prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/contracts', [EmployeeController::class, 'contracts'])->name('employees.contracts');
    Route::get('/teams', [EmployeeController::class, 'teams'])->name('employees.teams');
    Route::get('/functions', [EmployeeController::class, 'functions'])->name('employees.functions');
});

Route::middleware(['permission:HR.create'])->prefix('employees')->group(function () {
    Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/', [EmployeeController::class, 'store_employee'])->name('employees.store_employee');

    Route::post('/contracts/{id}', [EmployeeController::class, 'updateEndTime'])->name('employee.contracts.updateEndDate');
    Route::get('/create-contract', [EmployeeController::class, 'create_employeecontract'])->name('employees.create_contract');
    Route::post('/contracts', [EmployeeController::class, 'store_contract'])->name('employees.store_contract');

    Route::get('/create-team', [EmployeeController::class, 'create_team'])->name('employees.create_team');
    Route::post('/teams', [EmployeeController::class, 'store_team'])->name('employees.store_team');

    Route::get('/create-function', [EmployeeController::class, 'create_function'])->name('employees.create_function');
    Route::post('/functions', [EmployeeController::class, 'store_function'])->name('employees.store_function');
});

// ======================= End Employee ====================== //

// ======================= Start Pick Up Point ====================== //

Route::get('/pickup', [PackageController::class, 'index'])->name('pickup.dashboard');
Route::get('/pickup/package/{id}', [PackageController::class, 'show'])->name('pickup.package.id');
Route::patch('/pickup/package/{id}', [PackageController::class, 'setStatusPackage'])->name('pickup.dashboard.setStatusPackage');

// ======================= End Pick Up Point ====================== //

// ======================= Start Airport ====================== //

Route::get('/packages', function () {
    return view('packages');
})->name('packages');

Route::get('/contract', [contractController::class, 'contractindex'])->name('contract');

Route::get('/contractcreate', [contractController::class, 'contractcreate'])->name('contractcreate');

Route::post('/contract', [contractController::class, 'store'])->name('contract.store');

Route::get('/flights', [FlightsController::class, 'flightindex'])->name('flights');

Route::get('/flightcreate', [flightscontroller::class, 'flightcreate'])->name('flightcreate');

Route::post('/flights', [flightscontroller::class, 'store'])->name('flight.store');

Route::get('/airport', [airportController::class, 'airportindex'])->name('airports');

// ======================= End Airport ====================== //

// ======================= Start Customer ====================== //

Route::middleware("auth")->group(function () {
    Route::get('/send-package', [PackageController::class, 'create'])
        ->name('packages.send-package');

    Route::post('/send-package', [PackageController::class, 'store'])
        ->name('package.store');

    Route::get('/package-label/{id}', [PackageController::class, 'generatePackageLabel'])->name('generate-package-label');

    Route::get('/my-packages', [PackageController::class, 'mypackages'])
        ->name('packages.mypackages');

    Route::get('/package/{id}', [PackageController::class, 'packagedetails'])
        ->name('packages.packagedetails');
});

//--------------------------------- Tracking Packages ---------------------------------//
Route::get('/track/{reference}', [TrackPackageController::class, 'track'])->name('track.package');
//--------------------------------- ENDTracking Packages ---------------------------------//

// ======================= End Customer ====================== //

use App\Http\Controllers\RouteCreatorController;


Route::get('/create-route', [RouteCreatorController::class, 'createRoute']);

Route::get('/dispatcher', [DispatcherController::class, 'index'])->name('dispatcher.index');

Route::get('/distribution-center/{id}', [DispatcherController::class, 'getDistributionCenterDetails']);
