<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Constants\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use App\Services\ImageService;

class AuthController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /* =========================================================================
    دالة التسجيل القديمة (تم تحويلها لتعليق)
    =========================================================================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:255',
        ]);

        $otp = rand(100000, 999999);
        $otp_expires_at = now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => UserRole::USER,
            'otp_code' => $otp,
            'otp_expires_at' => $otp_expires_at,
        ]);

        // Send OTP Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            // Log error
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Account created. Please verify your email with the OTP sent.',
        ], 201);
    }
    =========================================================================
    */

    // =========================================================================
    // 1. إرسال كود التحقق قبل إنشاء الحساب (باستخدام Cache)
    // =========================================================================
    public function sendRegistrationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email' // التأكد أن الإيميل غير مستخدم
        ]);

        $otp = rand(100000, 999999);

        // حفظ الكود في الذاكرة المؤقتة للسيرفر لمدة 10 دقائق وليس في قاعدة البيانات
        Cache::put('otp_register_' . $request->email, $otp, now()->addMinutes(10));

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email'], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال رمز التحقق للتسجيل بنجاح.'
        ], 200);
    }

    // =========================================================================
    // 2. إنشاء الحساب الفعلي بعد التأكد من صحة الكود
    // =========================================================================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // تأكد أن فلاتر يرسل password_confirmation أو احذف confirmed
            'phone' => 'required|string|max:255',
            'otp' => 'required|string' 
        ]);

        $cachedOtp = Cache::get('otp_register_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => UserRole::USER,
            'email_verified_at' => now(), 
        ]);

        Cache::forget('otp_register_' . $request->email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'تم إنشاء الحساب وتفعيله بنجاح.',
        ], 201);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email' 
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = rand(100000, 999999);

        $user->otp_code = (string) $otp; 
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email'], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني بنجاح.'
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || $user->otp_code !== $fields['otp']) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP Expired'], 400);
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendOtp(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email'], 500);
        }

        return response()->json(['message' => 'OTP resent successfully']);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /* =========================================================================
    دالة تسجيل الخروج القديمة (التي كانت تسبب الخطأ)
    =========================================================================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    ========================================================================= */

    // =========================================================================
    // الدالة الآمنة والنهائية لتسجيل الخروج (تحل مشكلة delete غير المعرفة)
    // =========================================================================
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // استخدام tokens()->delete() يحذف جميع رموز الدخول من قاعدة البيانات بشكل آمن
            // ولا يسبب خطأ أبداً حتى لو لم يتم التعرف على الرمز الحالي
            $user->tokens()->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated or no token provided'
        ], 401);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا كانت موجودة في التخزين المحلي
            if ($user->avatar) {
                $avatarData = $user->avatar;
                if (is_array($avatarData)) {
                    foreach ($avatarData as $path) {
                        if (!empty($path) && is_string($path) && !str_starts_with($path, 'http')) {
                            $this->imageService->deleteImage($path);
                        }
                    }
                } elseif (is_string($avatarData) && !str_starts_with($avatarData, 'http')) {
                    $this->imageService->deleteImage($avatarData);
                }
            }

            $images = $this->imageService->processImage($request->file('avatar'), 'avatars');
            $user->avatar = $images;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Avatar updated successfully',
                'avatar_urls' => $user->avatar_urls,
                'user' => $user
            ]);
        }

        return response()->json(['status' => false, 'message' => 'No file uploaded'], 400);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($otp));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP'], 500);
        }

        return response()->json(['message' => 'OTP sent to your email']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->otp_code !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP Expired'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    // =========================================================================
    // Google Authentication
    // =========================================================================

    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle Google authentication callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            return $this->loginOrCreateUser($googleUser);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Google authentication failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Handle Google authentication from Mobile (using access token or id token).
     */
    public function handleGoogleToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // Using Socialite to get user from token
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);
            
            return $this->loginOrCreateUser($googleUser);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Google token: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Find or create user and return token.
     */
    protected function loginOrCreateUser($googleUser)
    {
        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        if ($user) {
            // Update user info if found
            $user->update([
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'role' => UserRole::USER,
                'email_verified_at' => now(),
                'password' => Hash::make(str()->random(24)), // Random password for social login
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}