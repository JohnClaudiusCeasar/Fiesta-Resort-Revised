<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function show(Request $request, $any = '')
    {
        $pages = [
            '' => 'admin/AdminOverview',
            'bookings' => 'admin/AdminBookings',
            'rooms' => 'admin/AdminRooms',
            'guests' => 'admin/AdminGuests',
        ];

        $component = $pages[$any] ?? 'admin/AdminOverview';

        // Fetch rooms for all pages
        $rooms = Room::all()->map(function ($room) {
            return [
                'id' => $room->id,
                'number' => $room->number,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'price_per_night' => $room->price_per_night,
                'status' => $room->status,
                'photo' => $room->photo,
                'discount' => $room->discount,
            ];
        });

        // Fetch guests data for all pages
        $guests = Guest::with(['bookings', 'bookings.room'])
            ->latest()
            ->get()
            ->map(function ($guest) {
                $this->updateGuestStatus($guest);

                $latestBooking = $guest->bookings()->latest('check_in')->first();

                return [
                    'id' => $guest->id,
                    'type' => $guest->type,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                    'nationality' => $guest->nationality,

                    'status' => $guest->status_label ?? $guest->status,
                    'createdAt' => $guest->created_at->toDateString(),

                    'totalBookings' => $guest->bookings()->count(),
                    'lastStay' => $latestBooking ? $latestBooking->check_in : 'No stays yet',

                    'bookings' => $guest->bookings()->latest()->take(5)->get()->map(function ($b) {
                        return [
                            'id' => $b->id,
                            'date' => $b->check_in,
                            'status' => $b->status,
                            'room' => $b->room?->number ?? 'Unassigned',
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
                    'id' => $booking->id,
                    'display_id' => str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'guest' => $booking->guest?->name ?? 'Unknown Guest',
                    'email' => $booking->guest?->email ?? 'N/A',
                    'room' => $booking->room
                                        ? ($booking->room->type.' '.$booking->room->number)
                                        : 'Unassigned',
                    'checkIn' => $booking->check_in,
                    'checkOut' => $booking->check_out,
                    'guestCount' => $booking->guest_count,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                ];
            });

        // Calculate overview stats
        $stats = [
            'todayBookings' => Booking::whereDate('check_in', today())->count(),
            'totalRooms' => Room::count(),
            'availableRooms' => Room::where('status', 'available')->count(),
            'occupiedRooms' => Room::where('status', 'occupied')->count(),
            'reservedRooms' => Room::where('status', 'reserved')->count(),
            'totalGuests' => Guest::count(),
            'totalRevenue' => Booking::where('status', 'Confirmed')->sum(DB::raw('ABS(total_price)')),
            'dailyRevenue' => Booking::where('status', 'Confirmed')->whereDate('check_in', today())->sum(DB::raw('ABS(total_price)')),
            'monthlyRevenue' => Booking::where('status', 'Confirmed')->whereMonth('check_in', now()->month)->whereYear('check_in', now()->year)->sum(DB::raw('ABS(total_price)')),
            'yearlyRevenue' => Booking::where('status', 'Confirmed')->whereYear('check_in', now()->year)->sum(DB::raw('ABS(total_price)')),
            'weeklyRevenue' => $this->getWeeklyRevenue(),
            'monthlyRevenueData' => $this->getMonthlyRevenueData(),
            'yearlyRevenueData' => $this->getYearlyRevenueData(),
        ];

        return Inertia::render($component, [
            'rooms' => $rooms,
            'guests' => $guests,
            'bookings' => $bookings,
            'stats' => $stats,
            'csrf_token' => csrf_token(),
        ]);
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

    public function guests()
    {
        // 1. Fetch Rooms (so the "Assign Room" dropdown has data)
        $rooms = Room::all()->map(function ($room) {
            return [
                'id' => $room->id,
                'number' => $room->number,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'price_per_night' => $room->price_per_night,
                'status' => $room->status,
            ];
        });

        // Fetch Guests
        $guests = Guest::latest()->get()->map(function ($guest) {
            return [
                'id' => $guest->id,
                'name' => $guest->name,
                'type' => $guest->type,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'nationality' => $guest->nationality,
                'status' => $guest->status_label,
                'createdAt' => $guest->created_at->toDateString(),
                'totalBookings' => $guest->bookings()->count(),
                'lastStay' => $guest->bookings()->latest()->first()?->check_in,
                'bookings' => [],
            ];
        });

        return inertia('admin/AdminGuests', [
            'guests' => $guests,
            'rooms' => $rooms,
        ]);
    }

    private function getWeeklyRevenue()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Booking::where('status', 'Confirmed')->whereDate('check_in', $date)->sum(DB::raw('ABS(total_price)'));
            $data[] = [
                'date' => $date,
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    private function getMonthlyRevenueData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Booking::where('status', 'Confirmed')
                ->whereMonth('check_in', $month->month)
                ->whereYear('check_in', $month->year)
                ->sum(DB::raw('ABS(total_price)'));
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    private function getYearlyRevenueData()
    {
        $data = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $revenue = Booking::where('status', 'Confirmed')->whereYear('check_in', $year->year)->sum(DB::raw('ABS(total_price)'));
            $data[] = [
                'year' => $year->year,
                'revenue' => $revenue,
            ];
        }

        return $data;
    }
}
