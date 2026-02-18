<?php

use App\Models\Car;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;

// Ensure we have a user and a car
$user = User::first();
if (!$user) {
    $user = User::factory()->create();
}

$car = Car::first();
if (!$car) {
    echo "No cars found. Please create a car first.\n";
    exit;
}

echo "Testing Booking for User: {$user->name} (ID: {$user->id})\n";
echo "Booking Car: {$car->name} (ID: {$car->id})\n";

$service = new BookingService();

$data = [
    'car_id' => $car->id,
    'start_date' => now()->addDays(1)->toDateString(),
    'end_date' => now()->addDays(3)->toDateString(),
];

try {
    echo "Attempting to create booking...\n";
    $result = $service->createBooking($user->id, $data);

    echo "Booking Created Successfully!\n";
    echo "Booking ID: " . $result['booking']->id . "\n";
    echo "Total Price: " . $result['booking']->total_price . "\n";
    echo "Stripe Client Secret: " . $result['paymentIntent']->client_secret . "\n";
    echo "Payment Intent ID: " . $result['paymentIntent']->id . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
