<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaxiPackage;

class TaxiPackagesSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name' => '10 km / 1 hr',
                'base_distance' => 10,
                'base_hours' => 1,
                'base_price' => 200,
                'extra_km_rate' => 15,
                'extra_hour_rate' => 50,
                'is_active' => true
            ],
            [
                'name' => '20 km / 2 hrs',
                'base_distance' => 20,
                'base_hours' => 2,
                'base_price' => 350,
                'extra_km_rate' => 12,
                'extra_hour_rate' => 40,
                'is_active' => true
            ],
            [
                'name' => '30 km / 3 hrs',
                'base_distance' => 30,
                'base_hours' => 3,
                'base_price' => 450,
                'extra_km_rate' => 10,
                'extra_hour_rate' => 30,
                'is_active' => true
            ]
        ];

        foreach ($packages as $package) {
            TaxiPackage::create($package);
        }
    }
}