<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}