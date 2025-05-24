<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'base_distance', 'base_hours', 
        'base_price', 'extra_km_rate', 'extra_hour_rate', 'is_active'
    ];
}
