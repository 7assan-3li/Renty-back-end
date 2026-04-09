<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\AuthController;


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/send-registration-otp', [AuthController::class, 'sendRegistrationOtp']);
// هذه الروابط محمية، لا يمكن الوصول إليها إلا للمستخدمين المسجلين
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);

    // أي روابط أخرى تحتاج تسجيل دخول توضع هنا
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// use App\Http\Controllers\PaymentController;

// Protected routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/password', [AuthController::class, 'changePassword']);

    // Category Routes
    Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

    // Car Routes
    Route::get('/cars', [\App\Http\Controllers\Api\CarController::class, 'index']);
    Route::get('/cars/favorites', [\App\Http\Controllers\Api\CarController::class, 'favorites']); // Specific route before wildcard
    Route::get('/cars/{id}', [\App\Http\Controllers\Api\CarController::class, 'show']);
    Route::post('/cars/{id}/favorite', [\App\Http\Controllers\Api\CarController::class, 'toggleFavorite']);


    // Payment Routes
    Route::post('/book', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::post('/bookings/{id}/rate', [\App\Http\Controllers\Api\BookingController::class, 'rate']);
    Route::post('/bookings/{id}/confirm', [\App\Http\Controllers\Api\BookingController::class, 'confirm']);
});
