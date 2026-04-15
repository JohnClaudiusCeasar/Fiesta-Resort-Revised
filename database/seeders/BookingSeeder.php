<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $guest = Guest::where('email', 'test@example.com')->first();

        if (! $guest) {
            $guest = Guest::create([
                'user_id' => $user->id,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '+1234567890',
                'nationality' => 'Philippines',
                'type' => 'online',
                'status' => 'staying',
            ]);
        }

        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $rooms = collect([
                Room::create(['number' => '101', 'name' => 'Garden View Suite', 'type' => 'Deluxe', 'capacity' => 2, 'price_per_night' => 150.00, 'status' => 'available', 'discount' => 0, 'photo' => null]),
                Room::create(['number' => '102', 'name' => 'Ocean View Room', 'type' => 'Deluxe', 'capacity' => 3, 'price_per_night' => 200.00, 'status' => 'available', 'discount' => 10, 'photo' => null]),
                Room::create(['number' => '103', 'name' => 'Family Bungalow', 'type' => 'Family', 'capacity' => 5, 'price_per_night' => 350.00, 'status' => 'available', 'discount' => 0, 'photo' => null]),
            ]);
        }

        $today = Carbon::today();

        $bookings = [
            [
                'room_id' => $rooms[0]->id,
                'check_in' => $today->copy()->addDays(5)->format('Y-m-d'),
                'check_out' => $today->copy()->addDays(8)->format('Y-m-d'),
                'guest_count' => 2,
                'status' => 'Pending',
                'payment_status' => 'pending',
                'notes' => 'Early check-in requested',
            ],
            [
                'room_id' => $rooms[1]->id,
                'check_in' => $today->copy()->addDays(14)->format('Y-m-d'),
                'check_out' => $today->copy()->addDays(17)->format('Y-m-d'),
                'guest_count' => 3,
                'status' => 'Confirmed',
                'payment_status' => 'paid',
                'notes' => 'Anniversary celebration',
            ],
            [
                'room_id' => $rooms[2]->id,
                'check_in' => $today->copy()->subDays(20)->format('Y-m-d'),
                'check_out' => $today->copy()->subDays(17)->format('Y-m-d'),
                'guest_count' => 4,
                'status' => 'Checked-Out',
                'payment_status' => 'paid',
                'notes' => 'Family vacation',
            ],
            [
                'room_id' => $rooms[0]->id,
                'check_in' => $today->copy()->subDays(5)->format('Y-m-d'),
                'check_out' => $today->copy()->subDays(2)->format('Y-m-d'),
                'guest_count' => 2,
                'status' => 'Cancelled',
                'payment_status' => 'pending',
                'notes' => 'Changed travel plans',
            ],
            [
                'room_id' => $rooms[1]->id,
                'check_in' => $today->copy()->addDays(1)->format('Y-m-d'),
                'check_out' => $today->copy()->addDays(3)->format('Y-m-d'),
                'guest_count' => 2,
                'status' => 'Confirmed',
                'payment_status' => 'paid',
                'notes' => null,
            ],
        ];

        foreach ($bookings as $index => $bookingData) {
            $room = Room::find($bookingData['room_id']);
            $checkIn = new Carbon($bookingData['check_in']);
            $checkOut = new Carbon($bookingData['check_out']);
            $nights = $checkOut->diffInDays($checkIn);
            $pricePerNight = $room->price_per_night;
            $discount = $room->discount ?? 0;
            $discountedPrice = $pricePerNight - ($pricePerNight * $discount / 100);
            $totalPrice = $discountedPrice * $nights;

            Booking::create([
                'guest_id' => $guest->id,
                'booking_reference' => 'FR-2026-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'room_id' => $bookingData['room_id'],
                'check_in' => $bookingData['check_in'],
                'check_out' => $bookingData['check_out'],
                'guest_count' => $bookingData['guest_count'],
                'total_price' => $totalPrice,
                'status' => $bookingData['status'],
                'payment_status' => $bookingData['payment_status'],
                'notes' => $bookingData['notes'],
            ]);
        }

        echo 'Seeded '.count($bookings)." bookings for test user (test@example.com)\n";
    }
}
