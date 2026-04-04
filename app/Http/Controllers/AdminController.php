<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function show(Request $request, $any = '')
    {
        $pages = [
            ''         => 'admin/AdminOverview',
            'bookings' => 'admin/AdminBookings',
            'rooms'    => 'admin/AdminRooms',
            'guests'   => 'admin/AdminGuests',
        ];

        $component = $pages[$any] ?? 'admin/AdminOverview';

        // Fetch rooms for all pages
        $rooms = Room::all()->map(function ($room){
            return[
                'id'               => $room->id,
                'number'           => $room->number,
                'name'             => $room->name,
                'type'             => $room->type,
                'capacity'         => $room->capacity,
                'price_per_night'  => $room->price_per_night,
                'available'        => $room->available,
                'photo'            => $room->photo,
            ];
        });

        // Fetch guests data for all pages
        $guests = Guest::withCount('bookings')
            ->latest()
            ->get()
            ->map(function ($guest) {
                // Get the most recent booking for this guest
                $latestBooking = $guest->bookings()->latest('check_in')->first();

                return [
                    'id'            => $guest->id,
                    'type'          => $guest->type,
                    'name'          => $guest->name,
                    'email'         => $guest->email,
                    'phone'         => $guest->phone,
                    'nationality'   => $guest->nationality,

                    'status'        => $guest->status_label ?? $guest->status,
                    'createdAt'     => $guest->created_at->toDateString(),

                    'totalBookings' => $guest->bookings_count,
                    'lastStay'      => $latestBooking ? $latestBooking->check_in : 'No stays yet',

                    'bookings'      => $guest->bookings()->latest()->take(5)->get()->map(function($b) {
                        return [
                            'id'    => $b->id,
                            'date'  => $b->check_in,
                            'status'=> $b->status,
                            'room'  => $b->room?->number ?? 'Unassigned'
                        ];
                    }),
                ];
            });

        // Fetch bookings data for overview page
        $bookings = Booking::with(['guest', 'room'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id'           => $booking->id,
                    'display_id'   => str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'guest'        => $booking->guest?->name ?? 'Unknown Guest',
                    'email'        => $booking->guest?->email ?? 'N/A',
                    'room'         => $booking->room    
                                        ? ($booking->room->type . ' ' . $booking->room->number) 
                                        : 'Unassigned',
                    'checkIn'      => $booking->check_in,
                    'checkOut'     => $booking->check_out,
                    'guestCount'   => $booking->guest_count,
                    'status'       => $booking->status,
                    'notes'        => $booking->notes,
                ];
            });

        return Inertia::render($component, [
            'rooms' => $rooms,
            'guests' => $guests,
            'bookings' => $bookings,
            'csrf_token' => csrf_token()
        ]);
    }

    public function guests()
    {
        // 1. Fetch Rooms (so the "Assign Room" dropdown has data)
        $rooms = Room::all()->map(function ($room) {
        return [
            'id'              => $room->id,
            'number'          => $room->number,
            'name'            => $room->name,
            'type'            => $room->type,
            'capacity'        => $room->capacity,
            'price_per_night' => $room->price_per_night,
            'available'       => $room->available,
        ];
    });

        // Fetch Guests
        $guests = Guest::latest()->get()->map(function ($guest) {
        return [
            'id'            => $guest->id,
            'name'          => $guest->name,
            'type'          => $guest->type,
            'email'         => $guest->email,
            'phone'         => $guest->phone,
            'nationality'   => $guest->nationality,
            'status'        => $guest->status_label,
            'createdAt'     => $guest->created_at->toDateString(),
            'totalBookings' => $guest->bookings()->count(),
            'lastStay'      => $guest->bookings()->latest()->first()?->check_in,
            'bookings'      => [],
        ];
    });
        
    return inertia('admin/AdminGuests', [
            'guests' => $guests,
            'rooms' => $rooms
        ]);
    }
}