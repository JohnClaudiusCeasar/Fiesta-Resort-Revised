<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // fillable fields for mass assignment to be saved in the database
    protected $fillable = [
        'guest_id',
        'room_id',
        'booking_reference',
        'check_in',
        'check_out',
        'guest_count',
        'total_price',
        'status',
        'payment_status',
        'notes',
    ];

    // Relationships
    // A booking belongs to a guest
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // A booking belongs to a room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
