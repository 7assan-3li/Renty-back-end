<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $imageService;

    public function __construct(\App\Services\ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'image' => ['nullable', 'image', 'max:51200'], // 50MB Max
        ]);

        if ($request->hasFile('image')) {
            // حذف الصور القديمة من الـ avatar والـ image
            if ($user->avatar) {
                $this->imageService->deleteImage($user->avatar);
            }
            if ($user->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->image);
            }

            // استخدام نظام المعالجة الجديد (صورتين WebP مضغوطة)
            $images = $this->imageService->processImage($request->file('image'), 'avatars');
            
            // تحديث الحقلين لضمان التوافق التام
            $user->avatar = $images;
            $user->image = $images['original'];
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Profile updated successfully'),
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Password changed successfully'),
        ]);
    }
}
