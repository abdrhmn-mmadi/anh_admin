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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\SaleController;

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

        /*
        | Dashboard
        */
        Route::get('/welcome', fn () => view('admin.welcome'))
            ->name('dashboard');

        /*
        | Users
        */
        Route::resource('users', UserController::class);

        /*
        | Departments & Services
        */
        Route::get('/departments', [DepartmentController::class, 'index'])
            ->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'storeDepartment'])
            ->name('departments.store');
        Route::post('/services', [DepartmentController::class, 'storeService'])
            ->name('services.store');
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroyDepartment'])
            ->name('departments.destroy');
        Route::delete('/services/{id}', [DepartmentController::class, 'destroyService'])
            ->name('services.destroy');
        Route::get('/departments/{department}/services', [DepartmentController::class, 'services'])
            ->name('departments.services');

        /*
        | Product Types / Employees / Banks
        */
        Route::resource('product-types', ProductTypeController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('banks', BankController::class);

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS CRUD
        |--------------------------------------------------------------------------
        */
        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');

        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])
            ->name('payments.edit');

        Route::put('/payments/{payment}', [PaymentController::class, 'update'])
            ->name('payments.update');

        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        /*
        | 🔍 SEARCH EMPLOYEE BY NIN (AJAX)
        */
        Route::post('/payments/search-employee',
            [PaymentController::class, 'searchEmployee']
        )->name('payments.searchEmployee');

        /*
        | 💰 PAY ALL EMPLOYEES (MONTHLY)
        */
        Route::post('/payments/pay-all',
            [PaymentController::class, 'payAll']
        )->name('payments.payAll');

        /*
        | Reports (reuse payments)
        */
        Route::get('/reports', [PaymentController::class, 'index'])
            ->name('reports.index');
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
        Route::get('/welcome', fn () => view('manager.welcome'))
            ->name('dashboard');
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

        Route::get('/dashboard', [AgentController::class, 'dashboard'])
            ->name('dashboard');

        /*
        | Sales
        */
        Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::post('/sales', [SaleController::class, 'store'])
            ->name('sales.store');

        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])
            ->name('sales.edit');

        Route::put('/sales/{sale}', [SaleController::class, 'update'])
            ->name('sales.update');

        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])
            ->name('sales.destroy');

        Route::get('/sales/{sale}/pdf', [SaleController::class, 'pdf'])
            ->name('sales.pdf');

        /*
        | Products
        */
        Route::get('/products', [AgentController::class, 'products'])
            ->name('products.index');

        Route::post('/products', [AgentController::class, 'store'])
            ->name('products.store');

        Route::put('/products/{id}', [AgentController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/{id}', [AgentController::class, 'destroy'])
            ->name('products.destroy');

        /*
        | Profile
        */
        Route::get('/profile', [AgentController::class, 'profile'])
            ->name('profile');

        Route::put('/profile/info', [AgentController::class, 'updateInfo'])
            ->name('profile.info.update');

        Route::put('/profile/password', [AgentController::class, 'updatePassword'])
            ->name('profile.password.update');
    });

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));
