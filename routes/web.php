<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsrfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockAlertController;
use Illuminate\Support\Facades\Route;

// Login & Register routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF token refresh endpoint (outside auth for offline sync)
Route::get('/api/csrf-token', [CsrfController::class, 'refresh']);

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::resource('products', ProductController::class);

    // Sales routes
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}/receipt', [SaleController::class, 'generateReceipt'])->name('sales.receipt');
    Route::get('/sales/{id}/receipt/pdf', [SaleController::class, 'downloadReceiptPdf'])->name('sales.receipt.pdf');

    // Quicksale
    Route::get('/quick-sale', [SaleController::class, 'quickSale'])->name('sales.quick');

    // Expenses & Profit Calculator
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Stock Alerts
    Route::get('/stock-alerts', [StockAlertController::class, 'index'])->name('stock-alerts.index');
    Route::post('/stock-alerts/threshold', [StockAlertController::class, 'updateThreshold'])->name('stock-alerts.threshold');
    Route::post('/stock-alerts/toggle', [StockAlertController::class, 'toggleAlerts'])->name('stock-alerts.toggle');
    Route::get('/stock-alerts/shopping-list', [StockAlertController::class, 'generateShoppingList'])->name('stock-alerts.shopping-list');

    // Sales Reports
    Route::get('/reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('/reports/download', [ReportController::class, 'downloadReport'])->name('reports.download');

    // Offline sync endpoints
    Route::post('/api/sync-sale', [SaleController::class, 'syncOfflineSale']);
    Route::post('/api/sync-product', [ProductController::class, 'syncOfflineProduct']);
});
