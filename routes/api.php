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
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']); // إضافة مسار حذف الإشعار

    // أي روابط أخرى تحتاج تسجيل دخول توضع هنا
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Google Auth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/token', [AuthController::class, 'handleGoogleToken']);

// use App\Http\Controllers\PaymentController;

// Category Routes (Temporarily Public for Testing)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']); // إضافة مسار تحديث الصورة
    Route::post('/profile/password', [AuthController::class, 'changePassword']);

    // Car Routes
    Route::get('/cars', [\App\Http\Controllers\Api\CarController::class, 'index']);
    Route::get('/cars/favorites', [\App\Http\Controllers\Api\CarController::class, 'favorites']); // Specific route before wildcard
    Route::get('/cars/{id}', [\App\Http\Controllers\Api\CarController::class, 'show']);
    Route::post('/cars/{id}/favorite', [\App\Http\Controllers\Api\CarController::class, 'toggleFavorite']);
    Route::post('/cars/{id}/star', [\App\Http\Controllers\Api\CarController::class, 'toggleStar']);


    // Payment Routes
    Route::post('/book', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::get('/bookings/{id}', [\App\Http\Controllers\Api\BookingController::class, 'show']); // إضافة هذا المسار
    Route::delete('/bookings/{id}', [\App\Http\Controllers\Api\BookingController::class, 'destroy']); // إضافة مسار الحذف
    Route::post('/bookings/{id}/rate', [\App\Http\Controllers\Api\BookingController::class, 'rate']);
    Route::post('/bookings/{id}/confirm', [\App\Http\Controllers\Api\BookingController::class, 'confirm']);
});
