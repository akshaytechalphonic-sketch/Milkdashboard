<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('/forgot-password', [ForgotPasswordController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
// Common auth-only group
Route::middleware(['auth'])->group(function () {

    Route::get('/generate-invoices', function () {
        Artisan::call('invoices:generate-monthly');
    });
    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::resource('user', UserController::class);
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

        Route::resource('category', CategoryController::class);
        Route::put('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
        // Route::post('/customers/{id}/toggle-pause', [CustomerController::class, 'togglePause'])->name('customers.togglePause');
        Route::post('/admin/customers/{id}/update-payment', [PaymentController::class, 'updatePayment'])->name('admin.customer.updatePayment');
        Route::put('/admin/payments/{id}', [PaymentController::class, 'update'])->name('admin.payments.update');

        // Invoice routes 
        Route::get('/customers/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        // Route::get('/invoices/generate/{customer_id}', [InvoiceController::class, 'generate'])->name('invoices.generate');

        Route::get('/invoices/generate/{customer}/{year}/{month}', [InvoiceController::class, 'generate'])
            ->name('invoices.generate');

        Route::get('/invoices/send-email/{customer}/{year}/{month}', [InvoiceController::class, 'sendEmail'])
            ->name('invoices.sendEmail');

        Route::get('/invoices/download/{id}', [InvoiceController::class, 'download'])->name('invoices.download');
        // Route::post('/invoices/mark-paid/{id}', [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');
        Route::post('/invoices/{invoice}/toggle-status', [InvoiceController::class, 'toggleStatus'])->name('invoices.toggleStatus');

        Route::get('/monthly-sheet', [InvoiceController::class, 'monthlySheet'])->name('monthly.sheet');
        Route::post('/admin/deliveries/update-monthly', [DeliveryController::class, 'updateMonthlyDeliveries'])->name('admin.deliveries.updateMonthly');
    });

    Route::get('/stocks', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stocks/{id}/edit', [StockController::class, 'edit'])->name('stock.edit');
    Route::put('/stocks/{id}', [StockController::class, 'update'])->name('stockupdate');
    Route::get('/stocks/update', [StockController::class, 'create'])->name('stock.create');
    Route::post('/stocks/update', [StockController::class, 'updateDailyStock'])->name('stock.update');
    Route::post('/customers/{id}/toggle-pause', [CustomerController::class, 'togglePause'])->name('customers.togglePause');
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.list');
    // Admin OR User can access deliveries
    Route::middleware(['role:admin,user'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('customers', CustomerController::class);
        Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
        Route::get('/get-rate', [DeliveryController::class, 'getRate'])->name('get.rate');
        Route::post('/deliveries/store', [DeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('/deliveries/edit/{id}', [DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::post('/deliveries/edit/{id}', [DeliveryController::class, 'update'])->name('deliveries.update');
        Route::post('/deliveries/delete/{id}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');
    });
    Route::get('/customer-deliveries', [DeliveryController::class, 'Deliverysheet'])->name('Delivery.sheet');
    Route::post('/customer-deliveries', [DeliveryController::class, 'delivery_store'])->name('customer.delivery');
});
