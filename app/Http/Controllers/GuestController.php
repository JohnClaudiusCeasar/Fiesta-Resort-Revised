<?php 

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    // Store a new walk-in guest (Admin manual Entry)
    public function store(Request $request)
    {
        $validated = $request->validate
        ([
            'name'        => 'required|string|max:200',
            'email'       => 'nullable|email|max:200',
            'phone'       => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100'
        ]);
        
        $guest = Guest::create([
            ...$validated,
            'type'        => 'walkin',
            'status'      => 'active'
        ]);

        return back()->with('success', "Guest \"{$guest->name}\" registered successfully.");
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
        $guest->delete();
        return back()->with('success', "Guest \" {$guest->name}\" has been deleted.");
    }

    // Toggle Blacklist Status
    public function blacklist(Guest $guest)
    {
        $guest->update(['status' => 'blacklisted']);

        return back()->with('success', "\"{$guest->name}\" has been blacklisted.");
    }
}