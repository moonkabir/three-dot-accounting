<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VendorBillController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Root & Authentication Routes
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes under Dashboard Management
Route::middleware('auth')->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::get('/create', [ItemController::class, 'create'])->name('create');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [ItemController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [ItemController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [ItemController::class, 'destroy'])->name('destroy');
        Route::post('/{inventory}/adjust-stock', [ItemController::class, 'adjustStock'])->name('adjust-stock');
        Route::post('/categories', [ItemController::class, 'storeCategory'])->name('categories.store');
    });

    // 3. Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{customer}/payments', [CustomerController::class, 'recordPayment'])->name('payments.store');
    });

    // 4. Customer Bills (Sales Invoicing)
    Route::prefix('customer-bills')->name('customer-bills.')->group(function () {
        Route::get('/', [CustomerBillController::class, 'index'])->name('index');
        Route::get('/create', [CustomerBillController::class, 'create'])->name('create');
        Route::post('/', [CustomerBillController::class, 'store'])->name('store');
        Route::get('/{customerBill}', [CustomerBillController::class, 'show'])->name('show');
        Route::get('/{customerBill}/print', [CustomerBillController::class, 'printInvoice'])->name('print');
        Route::post('/{customerBill}/payments', [CustomerBillController::class, 'recordPayment'])->name('payments.store');
        Route::delete('/{customerBill}', [CustomerBillController::class, 'destroy'])->name('destroy');
    });

    // 5. Vendor Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/create', [VendorController::class, 'create'])->name('create');
        Route::post('/', [VendorController::class, 'store'])->name('store');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
        Route::put('/{vendor}', [VendorController::class, 'update'])->name('update');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
        Route::post('/{vendor}/payments', [VendorController::class, 'recordPayment'])->name('payments.store');
    });

    // 6. Vendor Bills (Purchases / Expenses)
    Route::prefix('vendor-bills')->name('vendor-bills.')->group(function () {
        Route::get('/', [VendorBillController::class, 'index'])->name('index');
        Route::get('/create', [VendorBillController::class, 'create'])->name('create');
        Route::post('/', [VendorBillController::class, 'store'])->name('store');
        Route::get('/{vendorBill}', [VendorBillController::class, 'show'])->name('show');
        Route::get('/{vendorBill}/print', [VendorBillController::class, 'printInvoice'])->name('print');
        Route::post('/{vendorBill}/payments', [VendorBillController::class, 'recordPayment'])->name('payments.store');
        Route::delete('/{vendorBill}', [VendorBillController::class, 'destroy'])->name('destroy');
    });

    // 7. Reports & Accounting
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/cash-book', [ReportController::class, 'cashBook'])->name('cash-book');
    });

    // 8. Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::put('/password', [SettingController::class, 'updatePassword'])->name('password');
    });
});
