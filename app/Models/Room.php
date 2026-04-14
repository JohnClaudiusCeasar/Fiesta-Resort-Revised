<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'number',
        'name',
        'type',
        'capacity',
        'price_per_night',
        'status',
        'available_from',
        'available_to',
        'photo',
        'discount',
    ];

    protected $casts = [
        'available_from' => 'date',
        'available_to' => 'date',
        'price_per_night' => 'decimal:2',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';

    const STATUS_UNAVAILABLE = 'unavailable';

    const STATUS_OCCUPIED = 'occupied';

    const STATUS_RESERVED = 'reserved';

    // Helper methods
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isOccupied()
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function isReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
