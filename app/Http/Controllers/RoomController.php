<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Get All Rooms
     */
    public function index()
    {
        $rooms = Room::all();
        return back()->with('rooms', $rooms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a New Room
     */
    public function store(Request $request)
    {
        // Validate ALL incoming rooms Data
        $validated = $request->validate([
            'number'           => 'required|string|unique:rooms|max:50',
            'name'             => 'required|string|max:200',
            'type'             => 'required|in:Standard,Deluxe,Suite,Family',
            'capacity'         => 'required|integer|min:1',
            'price_per_night'  => 'required|numeric|min:0',
            'photo'            => 'nullable|url|max:500',
            'available'        => 'nullable|boolean',
            'available_from'   => 'nullable|date|after_or_equal:today',
            'available_to'     => 'nullable|date|after_or_equal:available_from',
            'discount'         => 'nullable|numeric|min:0|max:100'
        ]);

        // Creates the room
        $room = Room::create([
            ...$validated,
            'available' => $request->boolean('available', true),
            'discount'  => $request->input('discount', 0),

        ]);

        return back()->with('success', "Room \"{$room->name}\" created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update a Room
     */
    public function update(Request $request, Room $room)
    {
        // Validate ALL incoming rooms data
        $validated = $request->validate([
            'number'           => 'required|string|unique:rooms,number,' . $room->id . '|max:50',
            'name'             => 'required|string|max:200',
            'type'             => 'required|in:Standard,Deluxe,Suite,Family',
            'capacity'         => 'required|integer|min:1',
            'price_per_night'  => 'required|numeric|min:0',
            'photo'            => 'nullable|url|max:500',
            'available'        => 'nullable|boolean',
            'available_from'   => 'nullable|date|date',
            'available_to'     => 'nullable|date|after_or_equal:available_from',
            'discount'         => 'nullable|numeric|min:0|max:100',
        ]);

        // Updates the room
        $room->update([
            ...$validated,
            'available'        => $request->boolean('available', true),
            'discount'         => $request->input('discount', 0)
        ]);

        // Return using Inertia back() - this triggers a refresh of the rooms data
        return back()->with('success', "Room \"{$room->name}\" updated successfully.");
    }

    /**
     * Delete A Room
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', "Room \"{$room->name}\" has been deleted.");
    }

    /**
     * Toggle availability
     */ 
    public function toggleAvailability(Room $room)
    {
        $room->update(['available' => !$room->available]);

        $status = $room->available ? 'available' : 'unavailable';
        return back()->with('success', "Room \"{$room->name}\" is now {$status}.");
    }
}
