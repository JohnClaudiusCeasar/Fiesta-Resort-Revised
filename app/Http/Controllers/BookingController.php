<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Updates the status of the booking (Confirm, Check-In, Check-Out, Cancel, etc.)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Confirmed,Checked-In,Checked-Out,Cancelled',
        ]);

        $booking->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Update full booking details (from the edit modal)
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'check_in'    => 'required|date',
            'check_out'   => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1',
            'status'      => 'required|string',
            'notes'       => 'nullable|string'
        ]);

        $booking->update($validated);

        return back()->with('success', 'Booking details updated successfully.');
    }

    /**
     * Remove a booking record
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back()->with('success', 'Booking deleted successfully.');
    }

    /**
     * ...
     */
    public function clientBookings() 
    {
        return Inertia::render('client/MyBookings', [
            'bookings' => []
        ]);
    }
}
