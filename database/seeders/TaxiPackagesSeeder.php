<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxiPackage;

class TaxiPackagesSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name' => 'On-Demand/General',
                'type' => 'local',
                'base_distance' => 0,
                'base_hours' => 0,
                'base_price' => 0,
                'extra_km_rate' => 40,
                'extra_hour_rate' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Quick Hop (3km/30min)',
                'type' => 'local',
                'base_distance' => 3,
                'base_hours' => 0.5,
                'base_price' => 100,
                'extra_km_rate' => 25,
                'extra_hour_rate' => 80,
                'is_active' => true     
            ],
            [
                'name' => 'City Cruiser (10km/1h)',
                'type' => 'local',
                'base_distance' => 10,
                'base_hours' => 1,
                'base_price' => 200,
                'extra_km_rate' => 18,
                'extra_hour_rate' => 60,
                'is_active' => true
            ],
            
            [
                'name' => 'Town Shuttle (25km/2h)',
                'type' => 'local',
                'base_distance' => 25,
                'base_hours' => 2,
                'base_price' => 350,
                'extra_km_rate' => 15,
                'extra_hour_rate' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Metro Express (50km/4h)',
                'type' => 'local',
                'base_distance' => 50,
                'base_hours' => 4,
                'base_price' => 600,
                'extra_km_rate' => 12,
                'extra_hour_rate' => 40,
                'is_active' => true
            ],
            
            [
                'name' => 'Road Tripper (100km/8h)',
                'type' => 'outstation',
                'base_distance' => 100,
                'base_hours' => 8,
                'base_price' => 1200,
                'extra_km_rate' => 10,
                'extra_hour_rate' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Cross-Country (200km/12h)',
                'type' => 'outstation',
                'base_distance' => 200,
                'base_hours' => 12,
                'base_price' => 2000,
                'extra_km_rate' => 8,
                'extra_hour_rate' => 25,
                'is_active' => true
            ]
            
        ];

        foreach ($packages as $package) {
            TaxiPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}