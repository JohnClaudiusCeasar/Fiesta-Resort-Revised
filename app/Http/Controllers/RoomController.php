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
        $validated = $request->validate([
            'number' => 'required|string|unique:rooms|max:50',
            'name' => 'required|string|max:200',
            'type' => 'required|in:Standard,Deluxe,Suite,Family',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'photo' => 'nullable|url|max:500',
            'status' => 'nullable|in:available,unavailable,occupied,reserved',
            'available_from' => 'nullable|date|after_or_equal:today',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $room = Room::create([
            ...$validated,
            'status' => $request->input('status', 'available'),
            'discount' => $request->input('discount', 0),
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
        $validated = $request->validate([
            'number' => 'required|string|unique:rooms,number,'.$room->id.'|max:50',
            'name' => 'required|string|max:200',
            'type' => 'required|in:Standard,Deluxe,Suite,Family',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'photo' => 'nullable|url|max:500',
            'status' => 'nullable|in:available,unavailable,occupied,reserved',
            'available_from' => 'nullable|date|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $room->update([
            ...$validated,
            'status' => $request->input('status', 'available'),
            'discount' => $request->input('discount', 0),
        ]);

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
     * Set room status
     */
    public function setStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,unavailable,occupied,reserved',
        ]);

        $room->update(['status' => $validated['status']]);

        $statusLabel = ucfirst($validated['status']);

        return back()->with('success', "Room \"{$room->name}\" is now {$statusLabel}.");
    }
}
