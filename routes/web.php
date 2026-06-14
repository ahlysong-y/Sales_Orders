<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// =========================================================
// ទំព័រដើម (ប្តូរពីបង្ហាញ welcome ទៅជាលោតទៅទំព័រ Login ហ្មង)
// =========================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// កែប្រែឱ្យវាហៅទៅកាន់ SalesOrderController ត្រង់ index method វិញ
Route::get('/dashboard', [\App\Http\Controllers\SalesOrderController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ម៉ូឌុលគ្រប់គ្រង Profile របស់អ្នកប្រើប្រាស់ (រៀបចំដោយ Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================================
// ម៉ូឌុល MIS របស់មេរៀនទី ៧ (ប្រើសញ្ញាដក - តាមស្តង់ដារ URL)
// =========================================================

// ១. ម៉ូឌុល Purchase Orders (ការទិញចូល)
Route::resource('purchase-orders', PurchaseOrderController::class);
Route::patch('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'markAsReceived'])
    ->name('purchase-orders.receive');

// ២. ម៉ូឌុល Sales Orders (ការលក់ចេញ)
Route::resource('sales-orders', SalesOrderController::class);
Route::patch('sales-orders/{salesOrder}/confirm', [SalesOrderController::class, 'confirm'])
    ->name('sales-orders.confirm');

// ៣. ម៉ូឌុល Invoices & Payments (វិក្កយបត្រ និងការទូទាត់)
Route::get('invoices/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf.download');
Route::get('invoices/{invoice}/pdf/stream', [InvoiceController::class, 'streamPdf'])->name('invoices.pdf.stream');
Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');

// ប្រព័ន្ធ Login/Register របស់ Laravel Breeze
require __DIR__ . '/auth.php';
