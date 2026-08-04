<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit', 'show']);
    Route::resource('items', ItemController::class);

    Route::controller(StockInController::class)->prefix('barang-masuk')->name('stock-in.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tambah', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{stockIn}', 'destroy')->name('destroy');
    });

    Route::controller(StockOutController::class)->prefix('barang-keluar')->name('stock-out.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tambah', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{stockOut}', 'destroy')->name('destroy');
    });

    Route::controller(StockTransferController::class)->prefix('perpindahan-gudang')->name('stock-transfer.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tambah', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{stockTransfer}', 'destroy')->name('destroy');
    });

    Route::controller(LoanController::class)->prefix('peminjaman')->name('loans.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tambah', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{loan}', 'show')->name('show');
        Route::post('/{loan}/setujui', 'approve')->name('approve');
        Route::post('/{loan}/tolak', 'reject')->name('reject');
        Route::get('/{loan}/kembalikan', 'returnForm')->name('return-form');
        Route::post('/{loan}/kembalikan', 'processReturn')->name('return');
    });

    Route::controller(MaintenanceController::class)->prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tambah', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/{maintenance}/selesai', 'complete')->name('complete');
        Route::delete('/{maintenance}', 'destroy')->name('destroy');
    });

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export/{type}', [ReportController::class, 'exportCsv'])->name('reports.export');

    Route::controller(BarcodeController::class)->prefix('barcode')->name('barcode.')->group(function () {
        Route::get('/scan', 'scanner')->name('scanner');
        Route::get('/lookup', 'lookup')->name('lookup');
        Route::get('/label/{item}', 'label')->name('label');
    });

    Route::controller(NotificationController::class)->prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{notification}/baca', 'markRead')->name('read');
        Route::post('/tandai-semua', 'markAllRead')->name('read-all');
    });

    // Admin-only: warehouses, transfers between them, and user management.
    // Requires the 'admin' middleware alias — see README.md, "Peran & Hak Akses".
    Route::middleware('admin')->group(function () {
        Route::resource('gudang', WarehouseController::class)->except(['create', 'edit', 'show'])
            ->parameters(['gudang' => 'warehouse'])->names('warehouses');
        Route::post('/gudang/{warehouse}/jadikan-utama', [WarehouseController::class, 'setDefault'])->name('warehouses.set-default');

        Route::resource('pengguna', UserController::class)->except(['create', 'edit', 'show'])
            ->parameters(['pengguna' => 'user'])->names('users');
        Route::post('/pengguna/{user}/toggle-aktif', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });
});
