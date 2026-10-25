<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/games/{game:slug}', [CatalogController::class, 'show'])->name('games.show');
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:5,1')->name('orders.store');
Route::get('/orders/{order:reference}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order:reference}/sandbox', [OrderController::class, 'sandbox'])->name('orders.sandbox')->middleware('throttle:10,1');
Route::post('/orders/{order:reference}/sandbox', [OrderController::class, 'confirmSandbox'])->name('orders.sandbox.confirm')->middleware('throttle:5,1');
Route::post('/webhooks/payments/{provider}', [WebhookController::class, 'payment'])->name('webhooks.payment');
Route::post('/webhooks/suppliers/{provider}', [WebhookController::class, 'supplier'])->name('webhooks.supplier');
Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('health');
use Inertia\Inertia;
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
