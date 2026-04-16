<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::with('bookings')->get()->map(function ($guest) {
            $user = User::where('email', $guest->email)->first();

            $guest->is_active = $user && $user->last_seen_at && $user->last_seen_at >= now()->subMinutes(5);

            $this->updateGuestStatus($guest);

            return $guest;
        });

        return Inertia::render('Admin/Guests', ['guest' => $guests]);
    }

    private function updateGuestStatus($guest)
    {
        if (in_array($guest->status, ['checked_out', 'blacklisted'])) {
            return;
        }

        $latestBooking = $guest->bookings()
            ->where('status', 'Confirmed')
            ->orderBy('check_in', 'desc')
            ->first();

        if (! $latestBooking) {
            return;
        }

        $today = now()->toDateString();
        $checkIn = $latestBooking->check_in;
        $checkOut = $latestBooking->check_out;

        $newStatus = $guest->status;

        if ($today >= $checkIn && $today <= $checkOut) {
            $newStatus = 'checked_in';
        } elseif ($today > $checkOut) {
            $newStatus = 'checked_out';
        }

        if ($newStatus !== $guest->status) {
            $guest->update(['status' => $newStatus]);
        }
    }

    // Store a new walk-in guest (Admin manual Entry)
    public function store(Request $request)
    {
        $createBooking = $request->boolean('create_booking');

        $rules = [
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',
            'create_booking' => 'nullable|boolean',
        ];

        if ($createBooking) {
            $rules['room_id'] = 'required|exists:rooms,id';
            $rules['check_in'] = 'required|date';
            $rules['check_out'] = 'required|date|after:check_in';
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $createBooking) {
            $guestData = [
                'name' => $validated['name'],
                'email' => $validated['email'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'nationality' => $validated['nationality'] ?: null,
                'type' => 'walk-in',
                'status' => 'staying',
            ];

            $guest = Guest::create($guestData);

            if ($createBooking && ! empty($validated['room_id'])) {
                Booking::create([
                    'guest_id' => $guest->id,
                    'room_id' => $validated['room_id'],
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                    'status' => 'Confirmed',
                    'guest_count' => 1,
                ]);

                return back()->with('success', "Guest \"{$guest->name}\" registered and booked successfully.");
            }

            return back()->with('success', "Guest \"{$guest->name}\" registered successfully.");
        });
    }

    // Update Guest Info
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
        ]);

        $guest->update($validated);

        return back()->with('success', 'Guest updated successfully.');
    }

    // Delete a Guest item
    public function destroy(Guest $guest)
    {
        if ($guest->bookings()->exists()) {
            return back()->with('error', 'Cannot delete guest with active or past bookings. Try deactivating them instead.');
        } else {
            $guest->delete();

            return back()->with('success', 'Guest deleted successfully.');
        }
    }

    // Toggle Blacklist Status
    public function blacklist(Guest $guest)
    {
        $guest->update(['status' => 'blacklisted']);

        return back()->with('success', "\"{$guest->name}\" has been blacklisted.");
    }
}
