<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'car', 'payment'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                \App\Constants\BookingStatus::CONFIRMED,
                \App\Constants\BookingStatus::CANCELLED,
                \App\Constants\BookingStatus::COMPLETED
            ]),
        ]);

        $booking->update(['status' => $validated['status']]);

        // Logic to update Car Status if Confirmation happens Today
        if ($validated['status'] === \App\Constants\BookingStatus::CONFIRMED) {
            $today = \Carbon\Carbon::today();
            $start = \Carbon\Carbon::parse($booking->start_date);
            $end = \Carbon\Carbon::parse($booking->end_date);

            if ($start->lessThanOrEqualTo($today) && $end->greaterThanOrEqualTo($today)) {
                $booking->car->update(['status' => \App\Constants\CarStatus::BOOKED]);
            }
        }

        // Logic to free Car Status if Cancelled or Completed
        if (in_array($validated['status'], [\App\Constants\BookingStatus::CANCELLED, \App\Constants\BookingStatus::COMPLETED])) {
            $booking->car->update(['status' => \App\Constants\CarStatus::AVAILABLE]);
            $booking->update(['finished' => 'Yes']);
        }

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }
}
