<?php

use App\Models\User;
use App\Models\Car;
use App\Services\BookingService;
use Carbon\Carbon;

$user = User::first();
// Create a fresh car to ensure no conflicts
$category = \App\Models\Category::first() ?? \App\Models\Category::factory()->create();
$car = Car::factory()->create([
    'category_id' => $category->id,
    'status' => \App\Constants\CarStatus::AVAILABLE
]);

echo "Created Fresh Car: {$car->name} (ID: {$car->id})" . PHP_EOL;

$service = new BookingService();
$data = [
    'car_id' => $car->id,
    'start_date' => Carbon::today()->format('Y-m-d'),
    'end_date' => Carbon::tomorrow()->format('Y-m-d'),
];

try {
    $result = $service->createBooking($user->id, $data);
    $booking = $result['booking'];

    // Confirm Payment
    $service->confirmPayment($booking, 'real_txn_' . time());

    echo "Booking Created: #{$booking->id}" . PHP_EOL;
    echo "Car: {$car->name} (ID: {$car->id})" . PHP_EOL;
    echo "Car Status: " . $booking->car->fresh()->status . PHP_EOL;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
