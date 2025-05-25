<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'base_distance', 'base_hours', 
        'base_price', 'extra_km_rate', 'extra_hour_rate', 'is_active'
    ];

    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLocal($query)
    {
        return $query->where('type', 'local');
    }

    public function scopeOutstation($query)
    {
        return $query->where('type', 'outstation');
    }
}
