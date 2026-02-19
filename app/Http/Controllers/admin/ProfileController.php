<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists and not default
            if ($user->image && Storage::exists($user->image)) {
                Storage::delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('profile-images', 'public');
        }

        $user->update($validated);

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
