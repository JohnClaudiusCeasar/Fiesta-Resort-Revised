<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Update full booking details (from the edit modal)
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1',
            'status' => 'required|string',
            'notes' => 'nullable|string',
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
     * Store a new booking (for authenticated users)
     */
    public function store(BookingRequest $request)
    {
        $user = Auth::user();

        $guest = Guest::where('email', $user->email)->first();

        if (! $guest) {
            $guest = Guest::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => 'online',
                'status' => 'staying',
            ]);
        }

        $validated = $request->validated();

        $room = Room::find($validated['room_id']);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkOut->diffInDays($checkIn);

        $pricePerNight = $room->price_per_night;
        $discount = $room->discount ?? 0;
        $discountedPrice = $pricePerNight - ($pricePerNight * $discount / 100);
        $totalPrice = $discountedPrice * $nights;

        $existingBooking = Booking::where('room_id', $validated['room_id'])
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        if ($existingBooking) {
            return back()->with('error', 'This room is not available for the selected dates. Please choose different dates or another room.');
        }

        $lastBooking = Booking::orderBy('id', 'desc')->first();
        $nextNumber = $lastBooking ? ((int) substr($lastBooking->booking_reference, -3) + 1) : 1;
        $bookingReference = 'FR-'.date('Y').'-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $booking = DB::transaction(function () use ($guest, $validated, $totalPrice, $bookingReference) {
            return Booking::create([
                'guest_id' => $guest->id,
                'room_id' => $validated['room_id'],
                'booking_reference' => $bookingReference,
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'guest_count' => $validated['guest_count'],
                'total_price' => $totalPrice,
                'status' => 'Pending',
                'payment_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('client.bookings')->with('success', "Booking created successfully! Your booking reference is {$bookingReference}. We will confirm your booking shortly.");
    }

    /**
     * ...
     */
    public function clientBookings(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return Inertia::render('client/MyBookings', [
                'bookings' => [],
            ]);
        }

        $guest = Guest::where('email', $user->email)->first();

        if (! $guest) {
            return Inertia::render('client/MyBookings', [
                'bookings' => [],
            ]);
        }

        $bookings = Booking::with('room')
            ->where('guest_id', $guest->id)
            ->orderBy('check_in', 'desc')
            ->get();

        $selectedRoom = null;
        if ($request->has('selectedRoom')) {
            $decodedRoom = json_decode($request->get('selectedRoom'), true);
            if ($decodedRoom) {
                $selectedRoom = Room::find($decodedRoom['id']);
                if ($selectedRoom) {
                    $selectedRoom = [
                        'id' => $selectedRoom->id,
                        'name' => $selectedRoom->name,
                        'number' => $selectedRoom->number,
                        'type' => $selectedRoom->type,
                        'capacity' => $selectedRoom->capacity,
                        'price_per_night' => $selectedRoom->price_per_night,
                        'photo' => $selectedRoom->photo,
                        'discount' => $selectedRoom->discount ?? 0,
                    ];
                }
            }
        }

        return Inertia::render('client/MyBookings', [
            'bookings' => $bookings,
            'selectedRoom' => $selectedRoom,
            'checkIn' => $request->get('checkIn'),
            'checkOut' => $request->get('checkOut'),
        ]);
    }
}
