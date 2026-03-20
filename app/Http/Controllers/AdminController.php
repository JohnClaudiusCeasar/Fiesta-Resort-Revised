<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guest;
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

        return Inertia::render($component);
    }

    public function guests()
    {
        $guests = Guest::latest()->get()->map(function ($guest) {
        return [
            'id'            => $guest->id,
            'type'          => $guest->type,
            'name'          => $guest->name,
            'email'         => $guest->email,
            'phone'         => $guest->phone,
            'nationality'   => $guest->nationality,
            'status'        => $guest->status_label,
            'createdAt'     => $guest->created_at->toDateString(),
            'totalBookings' => 0,
            'lastStay'      => null,
            'bookings'      => [],
        ];
    });
        
    return inertia('admin/AdminGuests', [
            'guests' => $guests,
        ]);
    }
}