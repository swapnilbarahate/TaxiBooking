<?php

// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'package_id', 'pickup_location', 'drop_location',
        'actual_distance', 'actual_duration', 'base_fare',
        'extra_km_charge', 'extra_hour_charge', 'total_fare', 'booking_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(TaxiPackage::class);
    }
}