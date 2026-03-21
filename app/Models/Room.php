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
        'available',
        'photo',
        'discount'
    ];

    protected $casts = [
        'available' => 'boolean',
        'price_per_night' => 'decimal:2'
    ];

    // Relationships
    public function bookings()
    {
        //return $this->hasMany(Booking::class);
    }
}
