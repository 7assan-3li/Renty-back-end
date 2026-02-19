<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CarController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});


Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth Routes
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware('admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('cars', CarController::class);
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/vehicles', function () {
            return "Vehicles Page (Coming Soon)";
        })->name('vehicles');

        Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'update']);

        Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');

        Route::get('/accounts', function () {
            return "Accounts Page (Coming Soon)";
        })->name('accounts');

        Route::get('/credit-cards', function () {
            return "Credit Cards Page (Coming Soon)";
        })->name('credit_cards');

        Route::get('/services', function () {
            return "Services Page (Coming Soon)";
        })->name('services');

        Route::post('/profile/update', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/password/update', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('password.update');
    });
});
