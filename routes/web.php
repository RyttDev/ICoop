<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesStatisticsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): RedirectResponse => redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ahli', [MemberController::class, 'index'])->name('members.index');
    Route::post('/ahli', [MemberController::class, 'store'])->name('members.store');
    Route::put('/ahli/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::get('/stock-management', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock-management/{item}/movement', [StockController::class, 'movement'])->name('stock.movement');
    Route::put('/stock-management/memo/update', [StockController::class, 'updateMemo'])->middleware('admin')->name('stock.memo.update');
    Route::post('/stock-management', [StockController::class, 'store'])->middleware('admin')->name('stock.store');
    Route::put('/stock-management/{item}', [StockController::class, 'update'])->middleware('admin')->name('stock.update');
    Route::delete('/stock-management/{item}', [StockController::class, 'destroy'])->middleware('admin')->name('stock.destroy');
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/kata-laluan', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware('admin')->group(function (): void {
        Route::get('/pengurusan-akaun', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::post('/pengurusan-akaun', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
        Route::put('/pengurusan-akaun/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/pengurusan-akaun/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::patch('/laporan/{report}/semak', [ReportController::class, 'review'])->name('reports.review');
        Route::get('/statistik-jualan', [SalesStatisticsController::class, 'index'])->name('sales.index');
        Route::get('/invois', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invois', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::patch('/invois/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
        Route::post('/invois/{invoice}/tukar-invois', [InvoiceController::class, 'convertQuotation'])->name('invoices.convert');
        Route::get('/invois/{invoice}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
        Route::get('/invois/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    });
});
