<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'email',
        'phone',
        'nationality',
        'status'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        //return $this->hasMany(Booking::class);
    }

    // Accessors - format status for the frontend
    public function getStatusLabelAttribute(): String
    {
        return match($this->status){
            'active'     => 'Active',
            'checked_in' => 'Checked In',
            'checked_out'=> 'Checked Out',
            'blacklisted'=> 'Blacklisted',
            default      => ucfirst($this->status)
        };
    }
}