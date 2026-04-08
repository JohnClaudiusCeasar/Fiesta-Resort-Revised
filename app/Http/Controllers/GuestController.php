<?php 

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guest;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class GuestController extends Controller
{

    public function index()
    {

        $guests = Guest::all()->map(function ($guest)
        {
            // Find the user with the same email
            $user = User::where('email', $guest->email)->first();

            // Check if they were seen in the last 5 minutes
            $guest->is_active = $user &&$ $user->last_seen_at && $user->last_seen_at >= now()->subMinutes(5);

            return $guest;
        });

        return Inertia::render('Admin/Guests', [ 'guest' => $guests]);
    }

    // Store a new walk-in guest (Admin manual Entry)
    public function store(Request $request)
    {
        // Validate Guest + Booking Data
        $validated = $request->validate
        ([
            'name'        => 'required|string|max:200',
            'email'       => 'nullable|email|max:200',
            'phone'       => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',

            // Booking fields (only required if creating a booking)
            'room_id'     => 'nullable|exists:rooms,id',
            'check_in'    => 'required_with:room_id|date',
            'check_out'   => 'required_with:room_id|date|after:check_in',
        ]);
        
        return DB::transaction(function() use ($validated, $request)
        {
            // Create Guest
            $guest = Guest::create
            ([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'phone'       => $validated['phone'],
                'nationality' => $validated['nationality'],
                'type'        => 'walk-in',
                'status'      => 'active'
            ]);

            // Create Booking
            if ($request->has('room_id') && $request->room_id)
            {
                Booking::create
                ([
                    'guest_id'    => $guest->id,
                    'room_id'     => $validated['room_id'],
                    'check_in'    => $validated['check_in'],
                    'check_out'   => $validated['check_out'],
                    'status'      => 'Confirmed',
                    'guest_count' => 1
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
            'name'        => 'required|string|max:200',
            'email'       => 'nullable|email|max:200',
            'phone'       => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100'
        ]);

        $guest->update($validated);
        
        return back()->with('success', 'Guest updated successfully.');
    }

    // Delete a Guest item
    public function destroy(Guest $guest)
    {
        if($guest->bookings()->exists()) {
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