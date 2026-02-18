<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use App\Models\User;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_bookings_page()
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Bookings Management');
    }

    public function test_admin_can_see_bookings_list()
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $user = User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'SUV', 'image' => 'suv.jpg']);
        $car = Car::create([
            'name' => 'Toyota RAV4',
            'image' => 'rav4.jpg',
            'price_per_day' => 100,
            'latitude' => 0,
            'longitude' => 0,
            'category_id' => $category->id,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'total_price' => 300,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

        $response->assertSee($user->name);
        $response->assertSee($car->name);
        $response->assertSee('Pending');
        $response->assertSee('$300.00');
    }
}
