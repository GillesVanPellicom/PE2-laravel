<?php


use App\Http\Middleware\Authenticate;
use App\Models\Package;
use Aws\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use Pnlinh\GoogleDistance\Facades\GoogleDistance;
use App\Http\Controllers\RouteCreatorController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\PackageListController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\TrackPackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\Flightscontroller;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\CourierRouteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TicketController;
use App\Models\Employee;
use App\Http\Controllers\CustomerController;

// ======================= Start Middleware ====================== //
    Route::middleware('auth')
        ->prefix('workspace')
        ->name('workspace.')
        ->group(function () {
            //Workspace Index
            Route::get('/', function () {

                if (auth()->user()->hasPermissionTo('*')) {
                    return view('real-homepage');
                } elseif (auth()->user()->hasAnyPermission(["courier.route", "scan.deliver", "courier.packages"])) {
                    return redirect()->route('workspace.courier');
                } elseif (auth()->user()->hasAnyPermission(['HR.checkall',"HR.create", "HR.assign"])) {
                    return redirect()->route('workspace.employees.index');
                } elseif (auth()->user()->hasAnyPermission(["pickup.view", "pickup.edit"])) {
                    return redirect()->route('workspace.pickup.dashboard');
                } elseif (auth()->user()->hasPermissionTo("airport.view")) {
                    return redirect()->route('workspace.airports');
                } elseif (auth()->user()->hasAnyPermission(["assign.courier"])) {
                    return redirect()->route('workspace.dispatcher.index');
                }else {
                    return redirect()->route('welcome');
                }
            })->name('index');
            //end of workspace index

            // => Courier Mobile app
            // I changed the index to the scan as default page for courier bcs it will show up
            // a login even when the user is logged
            Route::get('/courier', [CourierController::class, 'scan'])
                ->name('courier');

            Route::post('/courier', function (Request $request) {
                return app(AuthController::class)->authenticate($request, 'courier.scan');
            })->name('courier.authenticate');

            Route::get('/courier/route', [CourierRouteController::class, 'showRoute'])
                ->middleware('permission:courier.route')
                ->name('courier.route');

            Route::get('/courier/packages', [CourierController::class, 'packages'])
                ->middleware('permission:courier.packages')
                ->name('courier.packages');

            Route::get('/courier/scan', [CourierController::class, 'scan'])
                ->middleware('permission:scan')
                ->name('courier.scan');

            Route::get('/courier/getlastpackages', [CourierController::class, 'getLastPackages'])
                ->middleware('permission:scan')
                ->name('courier.lastPackages');

            Route::post('/courier/scanQr', [CourierController::class, 'scanQr'])
                ->middleware('permission:scan')
                ->name('courier.scanQr');

            Route::post('/courier/deliver/{id}', [TrackPackageController::class, 'deliverPackage'])
                ->middleware('permission:scan.deliver')
                ->name('courier.deliver');

            Route::get('/courier/logout', [AuthController::class, 'logout'])
                ->middleware('permission:scan')
                ->name('courier.logout');

            Route::get("/courier/generate/{id}", [PackageController::class, "generateQRcode"])->name("generateQR");

            //Route::get('/courier/route', [CourierRouteController::class, 'showRoute'])->name('courier.route');

            //Route::get('/distribution-center/{id}/packages', [CourierRouteController::class, 'getDistributionCenterPackages'])->name('distribution-center.packages');

            Route::post('/courier/deliver/{id}', [CourierRouteController::class, 'deliver'])->name('courier.deliver');

            Route::get('/courier/signature/{id}', [CourierRouteController::class, 'signature'])->name('courier.signature');

            Route::post('/courier/submit-signature', [CourierRouteController::class, 'submitSignature'])->name('courier.submitSignature');




            // Route::post('/update-package-status', [PackageController::class, 'updateStatus'])->name('package.update');

            // ======================= End Courier ====================== //

            // ======================= Start Distribution ====================== //

            Route::get('/packagechart', [ChartController::class, 'getPackageData'])->name('package.chart');

            Route::get('/packagelist', [PackageListController::class, 'index'])->name('package.list');

            // ======================= End Distribution ====================== //

            // ======================= Start Employee ====================== //

            Route::middleware(['permission:employee'])->group(function () {
                Route::post('/save-vacation', [VacationController::class, 'saveVacation'])->name('vacation.save');
                Route::post('/save-vacation', [VacationController::class, 'store'])->name('vacation.store');
                Route::get('/approved-vacations', [VacationController::class, 'getApprovedVacations']);
                Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
                Route::get('/employees/calendar', [NotificationController::class, 'showCalendar'])->name('employees.calendar');
                Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
                Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');
                Route::get('/get-vacations', [VacationController::class, 'getVacations'])->name('get-vacations');
                Route::post('/vacations/{id}/update-status', [VacationController::class, 'updateStatus'])->name('vacations.updateStatus');
            });

            Route::middleware(['permission:HR.create'])->group(function(){
                Route::get('/manager-calendar', [EmployeeController::class, 'managerCalendar'])->name('manager.calendar');

                Route::get('/pending-vacations', [VacationController::class, 'getPendingVacations']);

                Route::post('/vacations/{id}/update-status', [VacationController::class, 'updateStatus']);

                Route::get('/employees/holiday-requests', [VacationController::class, 'showAllVacations'])->name('employees.holiday_requests');

                Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');

                Route::get('/vacations/sick-leaves', [VacationController::class, 'getSickLeaves'])->name('vacations.sickLeaves');

                Route::get('/manager/sick-day-notifications', [NotificationController::class, 'fetchSickDayNotifications'])->name('sickLeave.fetch');
                Route::put('/manager/sick-day-notifications/{id}/read', [NotificationController::class, 'markSickLeaveAsRead'])->name('sickLeave.markAsRead');

                Route::get('/manager/sick-day-notifications', [NotificationController::class, 'fetchSickDayNotifications'])->name('sickLeave.fetch');
                Route::put('/manager/sick-day-notifications/{id}/read', [NotificationController::class, 'markSickLeaveAsRead'])->name('sickLeave.markAsRead');

                Route::get('/manager/sick-day-notifications', [NotificationController::class, 'fetchSickDayNotifications'])->name('sickLeave.fetch');
                Route::put('/manager/sick-day-notifications/{id}/read', [NotificationController::class, 'markSickLeaveAsRead'])->name('sickLeave.markAsRead');


            });

            Route::middleware(['permission:HR.checkall'])->prefix('employees')->group(function () {
                Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
                Route::get('/contracts', [EmployeeController::class, 'contracts'])->name('employees.contracts');
                Route::get('/teams', [EmployeeController::class, 'teams'])->name('employees.teams');
                Route::get('/functions', [EmployeeController::class, 'functions'])->name('employees.functions');
                Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
                Route::get('/searchContract', [EmployeeController::class, 'searchContract'])->name('employees.searchContract');
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

            Route::middleware(['permission:HR.create'])->group(function () {
                Route::post('/contracts/{id}', [EmployeeController::class, 'updateEndTime'])->name('contracts.updateEndTime');
            });

            Route::get('/get-availability-data', [EmployeeController::class, 'getAvailabilityData'])->name('availability.data');

            Route::get('/get-unavailable-employees', [EmployeeController::class, 'getUnavailableEmployees'])->name('unavailable.employees');


            Route::get('/sick-leave-notifications', [NotificationController::class, 'fetchSickDayNotifications'])->name('sickLeaveNotifications.fetch');
            Route::post('/sick-leave-notifications/{id}/mark-as-read', [NotificationController::class, 'markSickLeaveAsRead'])->name('sickLeaveNotifications.markAsRead');

            Route::get('/workspace/sick-leave-notifications', [VacationController::class, 'getSickLeaveNotifications']);
            Route::post('/workspace/sick-leave-notifications/{id}/mark-as-read', [VacationController::class, 'markSickLeaveAsRead']);

            // contract PDF
            Route::get('/contract/{id}', [EmployeeController::class, 'generateEmployeeContract'])->name('employees-contract-template');

            // Route for fetching employee notifications
            Route::get('/employee-notifications', [NotificationController::class, 'fetchNotifications'])->name('employee.notifications');

            // Route for fetching pending vacations
            Route::get('/pending-vacations', [VacationController::class, 'getPendingVacations'])->name('pending.vacations');

            // Route for fetching sick leave notifications
            Route::get('/sick-leave-notifications', [NotificationController::class, 'fetchSickDayNotifications'])->name('sick.leave.notifications');

            // Route for general notifications
            Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->middleware('auth')->name('notifications');

            // Notifications
            Route::get('/notifications', [NotificationController::class, 'fetchNotifications'])->name('workspace.notifications');

            Route::get('/workspace/get-pending-requests-for-day', [VacationController::class, 'getPendingRequestsForDay'])->name('workspace.getPendingRequestsForDay');

            Route::get('/workspace/get-pending-vacations', [VacationController::class, 'getPendingVacations']);

            Route::post('/workspace/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');


            // ======================= End Employee ====================== //

            // ======================= Start Pick Up Point ====================== //
            Route::middleware(['permission:pickup.view'])->group(function () {
                Route::get('/pickup', [PackageController::class,'index'])->name('pickup.dashboard');
                Route::get('/pickup/package/{id}', [PackageController::class,'show'])->name('pickup.package.id');
                Route::patch('/pickup/package/{id}', [PackageController::class,'setStatusPackage'])->name('pickup.dashboard.setStatusPackage');
                Route::get('pickup/dashboard/receiving-packages', [PackageController::class,'showReceivingPackages'])->name('pickup.dashboard.receiving-packages');
                Route::get('pickup/dashboard/packages-to-return', [PackageController::class,'showPackagesToReturn'])->name('pickup.dashboard.packages-to-return');

            });
            Route::get('testDeliveryAttemptOnWrongLocation/{id}', [PackageController::class,'testDeliveryAttemptOnWrongLocation'])->name('testDeliveryAttemptOnWrongLocation');
            Route::get('testDeliveryAttemptOnWrongLocation', [PackageController::class,'testDeliveryAttemptOnWrongLocation'])->name('testDeliveryAttemptOnWrongLocationHome');
            // ======================= End Pick Up Point ====================== //

            // ======================= Start Airport ====================== //
            Route::middleware(['permission:airport.view'])->group(function () {
                Route::get('/contract', [ContractController::class, 'contractindex'])->name('contract');

                Route::get('/contractcreate', [ContractController::class, 'contractcreate'])->name('contractcreate');

                Route::post('/contract', [ContractController::class, 'store'])->name('contract.store');

                Route::get('/flights', [FlightsController::class, 'flightindex'])->name('flights');

                Route::get('/flightcreate', [Flightscontroller::class, 'flightcreate'])->name('flightcreate');

                Route::post('/flights', [Flightscontroller::class, 'store'])->name('flight.store');

                Route::patch('/flights/{id}/update-status', [Flightscontroller::class, 'updateStatus'])->name('flights.updateStatus');
                Route::post('/assign-flight', [Flightscontroller::class, 'assignFlight'])->name('assign-flight');

                Route::get('/flightpackages', [FlightsController::class, 'flightPackages'])->name('flightpackages');
                Route::get('/airlines', [Flightscontroller::class, 'flights'])->name('airlines.flights');

                Route::patch('/flightContracts/{id}/updateEndDate', [Flightscontroller::class, 'updateContractEndDate'])->name('flightContracts.updateEndDate');

                Route::get('/airports', [Flightscontroller::class, 'airports'])->name('airports');
            });
            // ======================= End Airport ====================== //


            Route::middleware(['permission:assign.courier'])->group(function () {
                Route::get('/create-route', [RouteCreatorController::class, 'createRoute']);

                Route::get('/dispatcher', [DispatcherController::class, 'index'])->name('dispatcher.index');
                Route::get('/distribution-center/{id}', [DispatcherController::class, 'getDistributionCenterDetails'])->name('dispatcher.details');
                Route::post('/distribution-center/dispatch-packages', [DispatcherController::class, 'dispatchSelectedPackages'])->name('dispatcher.dispatch-packages');
                Route::post('/distribution-center/unassign-packages', [DispatcherController::class, 'unassignPackages'])->name('dispatcher.unassign-packages');
                Route::post('/distribution-center/calculate-optimal-selection', [DispatcherController::class, 'calculateOptimalSelection'])->name('dispatcher.calculate-optimal');
                Route::get('/distribution-center/courier-route/{id}', [DispatcherController::class, 'getCourierRoute'])->name('dispatcher.courier-route');
                Route::get('/distribution-center/{id}/couriers', [DispatcherController::class, 'getCouriersForDC'])->name('dispatcher.couriers');
            });

            // ======================= End CourierRouteCreator ====================== //

            Route::get('/stranded-packages', [PackageController::class, 'strandedPackages'])->name('stranded-packages');
            Route::post('/stranded-packages', [PackageController::class, 'reRouteStrandedPackages'])->name('stranded-packages.reRoute');
        });
// ======================= End Middleware ====================== //


// ======================= Start Authentication ====================== //

Route::get('/', function () {
    return view('real-homepage');
})->name('welcome');

// Login
Route::get('/login', function () {
    return view('auth.login');
})->middleware("guest")->name('auth.login');

Route::post('/login', function (Request $request) {
    return app(AuthController::class)->authenticate($request);
})->name('auth.authenticate');

// Register
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'store'])->name('auth.store');

// Update
Route::post('/update', [AuthController::class, 'update'])->name('auth.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get("/logout", fn() =>
    redirect("login")
);

// Customers
Route::middleware("auth")->group(function () {
    Route::get('/profile', [AuthController::class, 'showCustomers'])->name('profile');
});

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

// ======================= End Authentication ====================== //

// ======================= Start Customer ====================== //
    Route::get('/send-package', [PackageController::class, 'create'])
        ->name('packages.send-package');

    Route::post('/send-package', [PackageController::class, 'store'])
        ->name('package.store');

    Route::post('/update-prices', [PackageController::class, 'updatePrices'])
        ->name('update-prices');

    Route::post('/package/{id}/return', [PackageController::class, 'returnPackage'])
        ->name('packages.return');

    Route::get('/package-label/{id}', [PackageController::class, 'generatePackageLabel'])->name('generate-package-label');



    Route::get('/package/{id}', [PackageController::class, 'packagedetails'])
        ->name('packages.packagedetails');

Route::middleware("auth")->group(function () {
    Route::get('/my-packages', [PackageController::class, 'mypackages'])
        ->name('packages.mypackages');

    Route::get('/bulk-order', [PackageController::class, 'bulkOrder'])
        ->name('packages.bulk-order');

    Route::post('/bulk-order', [PackageController::class, 'storeBulkOrder'])
        ->name('packages.bulk-order.store');

    Route::get('/packages/bulk-details/{ids}', [PackageController::class, 'bulkPackageDetails'])
        ->name('packages.bulk-details');

    Route::get('/company-dashboard', [PackageController::class, 'companyDashboard'])
        ->middleware(['permission:business_client.view'])
        ->name('packages.company-dashboard');

    Route::post('/packages/complete-bulk-payment', [PackageController::class, 'completeBulkPayment'])
        ->name('packages.complete-bulk-payment');

    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware(['permission:business_client.view'])
        ->name('customers.index');
        // invoice start
        Route::get('/invoices',[InvoiceController::class, 'manageInvoices'])->name('manage-invoices');
        Route::get('/invoice-payment', [InvoiceController::class, 'getUnpaidInvoices'])->name("manage-invoice-system");
        Route::post('/invoices/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid');
        // invoice end
});

// Invoices

// Route::middleware('auth')
//     ->group(function () {
//         Route::get('/invoice/{id}', [InvoiceController::class, 'generateInvoice'])
//         ->middleware(['permission:business_client.view'])
//         ->name('generate-invoice');

//         Route::get('/my-invoices', [InvoiceController::class, 'myinvoices'])
//         ->middleware(['permission:business_client.view'])
//         ->name('invoices.myinvoices');
//     });

Route::get('/invoice/{id}', [InvoiceController::class, 'generateInvoice'])->name('generate-invoice');

Route::get('/my-invoices', [InvoiceController::class, 'myinvoices'])
->name('invoices.myinvoices');
Route::get('/invoices',[InvoiceController::class, 'manageInvoices'])->name('manage-invoices');
// Tickets

Route::get('/tickets', [TicketController::class, 'mytickets'])
->name('tickets.nytickets');

Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

Route::get('/tickets/{id}', [TicketController::class, 'ticketchat'])
->name('tickets.ticketchat');

Route::post('/tickets/{id}', [TicketController::class, 'newmessage'])->name('tickets.newmessage');

//--------------------------------- Tracking Packages ---------------------------------//
Route::get('/track/{reference}', [TrackPackageController::class, 'track'])->name('track.package');
//--------------------------------- ENDTracking Packages ---------------------------------//

// ======================= End Customer ====================== //

// ======================= Package Payment Start  ====================== //

Route::get('/package/payment/{id}', [PackageController::class, 'packagePayment'])
    //->middleware('auth')
    ->name('packagepayment');

// ======================= Package Payment End  ====================== //
    Route::get('/track-parcel',[TrackPackageController::class, 'trackParcel'])->name('track-parcel');

// API Start

Route::post('/tokens/create', function (Request $request) {
    $request->user()->tokens()->where("name", "api")->delete();
    $token = $request->user()->createToken("api");
    return response()->json(['token' => $token->plainTextToken]);
})->name("tokens.create");

// API End

// Route for fetching pending vacations
Route::get('/pending-vacations', [VacationController::class, 'getPendingVacations']);

// Route for fetching pending requests for a specific day
Route::get('/workspace/get-pending-requests-for-day', [VacationController::class, 'getPendingRequestsForDay']);

Route::post('/workspace/send-end-of-year-notifications', [NotificationController::class, 'sendEndOfYearNotifications'])
->middleware('auth')
->name('workspace.sendEndOfYearNotifications');

Route::get('/workspace/end-of-year-notifications', [NotificationController::class, 'fetchEndOfYearNotifications'])
->middleware('auth')
->name('workspace.endOfYearNotifications');

Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('workspace.notifications.read');

Route::post('/workspace/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

Route::post('/workspace/mark-employee-sick/{employee}', [VacationController::class, 'markEmployeeAsSick'])->name('markEmployeeAsSick');

Route::view('/courier-location', 'courierlocationchange')->name('courier.location-change');

Route::post("/courier/update-location", function (Request $request){
    $employee = Employee::find(10);
    $package = Package::find(405);
    $employee->courierRoute->current_location = $package->destination_location_id;
    $employee->courierRoute->save();
    return redirect()->route('courier.location-change');
})->name("courier.update.location");



Route::middleware(['permission:assign.courier'])->group(function () {
    Route::get('/create-route', [RouteCreatorController::class, 'createRoute']);

    Route::get('/dispatcher', [DispatcherController::class, 'index'])->name('dispatcher.index');
    Route::get('/distribution-center/{id}', [DispatcherController::class, 'getDistributionCenterDetails'])->name('dispatcher.details');
    Route::post('/distribution-center/dispatch-packages', [DispatcherController::class, 'dispatchSelectedPackages'])->name('dispatcher.dispatch-packages');
    Route::post('/distribution-center/unassign-packages', [DispatcherController::class, 'unassignPackages'])->name('dispatcher.unassign-packages');
    Route::post('/distribution-center/calculate-optimal-selection', [DispatcherController::class, 'calculateOptimalSelection'])->name('dispatcher.calculate-optimal');
});


// Route for FAQ page

Route::get('/faq', function () {
    return view('faq');
})->name('faq');