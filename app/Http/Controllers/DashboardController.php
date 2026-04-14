<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function show()
    {

        $rooms = Room::where('status', 'available')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'number' => $room->number,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'price_per_night' => $room->price_per_night,
                'photo' => $room->photo,
                'discount' => $room->discount ?? 0,
            ];
        });

        return Inertia::render('client/Dashboard', [
            'user' => auth()->check() ? auth()->user() : null,
            'rooms' => $rooms,
        ]);
    }
}
