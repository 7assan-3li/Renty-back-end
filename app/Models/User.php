<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'role',
        'latitude',
        'longitude',
        'otp_code',
        'otp_expires_at',
        'google_id',
        'avatar',
        'status',
        'balance',
    ];

    protected $appends = ['avatar_urls'];

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === \App\Constants\UserRole::ADMIN;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'avatar' => 'array',
        ];
    }

    public function favorites()
    {
        return $this->belongsToMany(Car::class, 'favorites', 'user_id', 'car_id')->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getAvatarUrlsAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        $images = $this->avatar;

        if (is_string($images) && str_starts_with($images, 'http')) {
            return [
                'original' => $images,
                'thumbnail' => $images,
                'small' => $images,
                'medium' => $images,
            ];
        }

        if (!is_array($images)) {
            $path = is_string($images) ? $images : null;
            if (!$path) return null;
            
            $fullUrl = str_starts_with($path, 'http') ? $path : url('storage/' . $path);
            return [
                'original' => $fullUrl,
                'thumbnail' => $fullUrl,
                'small' => $fullUrl,
                'medium' => $fullUrl,
            ];
        }

        return array_map(function ($path) {
            if (empty($path)) return null;
            return str_starts_with($path, 'http') ? $path : url('storage/' . $path);
        }, $images);
    }
}
