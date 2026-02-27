<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DepenseController;
use App\Http\Controllers\Admin\AdminSalesController;

use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\SaleController;

use App\Http\Controllers\Manager\ManagerEmployeeController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\ManagerProductController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ManagerSalesController;



Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('sales', [AdminSalesController::class, 'index'])->name('admin.sales');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => view('login'))->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);

        return match ($user->role?->name) {
            'Admin'   => redirect()->route('admin.dashboard'),
            'Manager' => redirect()->route('manager.dashboard'),
            'Agent'   => redirect()->route('agent.dashboard'),
            default   => redirect()->route('login')
                ->withErrors(['email' => 'Role not recognized']),
        };
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        // Dashboard
        Route::get('/welcome', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class);

        // Departments & Services
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
        Route::post('/services', [DepartmentController::class, 'storeService'])->name('services.store');
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');
        Route::delete('/services/{id}', [DepartmentController::class, 'destroyService'])->name('services.destroy');
        Route::get('/departments/{department}/services', [DepartmentController::class, 'services'])->name('departments.services');

        // Products / Employees / Banks
        Route::resource('product-types', ProductTypeController::class);

        // ✅ EMPLOYEES
        // Static route first (export)
        Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');

        // Then resource route (index, create, store, show, edit, update, destroy)
        Route::resource('employees', EmployeeController::class);

        // Banks
        Route::resource('banks', BankController::class);

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('/payments/search-employee', [PaymentController::class, 'searchEmployee'])->name('payments.searchEmployee');
        Route::post('/payments/pay-all', [PaymentController::class, 'payAll'])->name('payments.payAll');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/payslip', [ReportController::class, 'payslip'])->name('reports.payslip');
        Route::get('/reports/bank', [ReportController::class, 'bank'])->name('reports.bank');
        Route::get('/reports/expense', [ReportController::class, 'expense'])->name('reports.expense');
        Route::get('/reports/complete', [ReportController::class, 'completeList'])->name('reports.complete');
        Route::get('/reports/complete/excel', [ReportController::class, 'completeExcel'])->name('reports.complete.excel');

        // Products (display only)
        Route::get('/products', [ProductTypeController::class, 'products'])->name('products.index');
        Route::get('/products/export', [ProductTypeController::class, 'export'])->name('products.export');

        // Depenses
        Route::get('/depenses', [DepenseController::class, 'index'])->name('depenses');
        Route::post('/depenses', [DepenseController::class, 'store'])->name('depenses.store');
        Route::put('/depenses/{depense}', [DepenseController::class, 'update'])->name('depenses.update');
        Route::delete('/depenses/{depense}', [DepenseController::class, 'destroy'])->name('depenses.destroy');
        Route::post('/depenses/pdf', [DepenseController::class, 'pdf'])->name('depenses.pdf');

        // Sales (Ventes)
        Route::get('sales/export', [AdminSalesController::class, 'export'])->name('sales.export');
        Route::get('sales', [AdminSalesController::class, 'index'])->name('sales.index');

    });

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/
Route::prefix('manager')
    ->name('manager.')
    ->middleware('auth')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/welcome', [ManagerDashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Employees
        |--------------------------------------------------------------------------
        */
        Route::get('/employees', [ManagerEmployeeController::class, 'index'])
            ->name('employees');

        Route::get('/employees/export', [ManagerEmployeeController::class, 'export'])
            ->name('employees.export');

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */
        Route::get('/profile', [ManagerController::class, 'profile'])
            ->name('profile');

        Route::put('/profile/info', [ManagerController::class, 'updateInfo'])
            ->name('profile.info.update');

        Route::put('/profile/password', [ManagerController::class, 'updatePassword'])
            ->name('profile.password.update');

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        Route::get('/products', [ManagerProductController::class, 'index'])
            ->name('products');

         // Sales (Ventes)
        Route::get('sales', [ManagerSalesController::class, 'index'])->name('sales.index');
        Route::get('sales/export', [ManagerSalesController::class, 'export'])->name('sales.export');


    });

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('agent')
    ->name('agent.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');

        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit'); // <--- add this
        Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::get('/sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');
        Route::get('/sales/search', [SaleController::class, 'search']);


        Route::get('/products', [AgentController::class, 'products'])->name('products.index');
        Route::post('/products', [AgentController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [AgentController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [AgentController::class, 'destroy'])->name('products.destroy');

        Route::get('/profile', [AgentController::class, 'profile'])->name('profile');
        Route::put('/profile/info', [AgentController::class, 'updateInfo'])->name('profile.info.update');
        Route::put('/profile/password', [AgentController::class, 'updatePassword'])->name('profile.password.update');
    });

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));



