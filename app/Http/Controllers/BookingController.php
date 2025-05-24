<?php

// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TaxiPackage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function calculateFare(Request $request)
    {
        $request->validate([
            'pickup' => 'required',
            'drop' => 'required',
            'package_id' => 'required|exists:taxi_packages,id'
        ]);

        $package = TaxiPackage::find($request->package_id);
        
        $response = $this->getDistanceMatrix($request->pickup, $request->drop);
        
        if (!$response['success']) {
            return response()->json(['error' => $response['message']], 400);
        }

        $distanceKm = $response['distance'] / 1000;
        $durationMinutes = $response['duration'] / 60;

        $fare = $this->calculateFinalFare($package, $distanceKm, $durationMinutes);

        return response()->json([
            'success' => true,
            'fare' => $fare,
            'distance' => round($distanceKm, 2),
            'duration' => round($durationMinutes, 2)
        ]);
    }

    private function getDistanceMatrix($origin, $destination)
    {
        $apiKey= env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$origin&destinations=$destination&key=$apiKey";

        try {
            $response = Http::get($url);
            $data = $response->json();

            if ($data['status'] !== 'OK') {
                return [
                    'success' => false,
                    'message' => 'Failed to get distance information from Google Maps'
                ];
            }

            return [
                'success' => true,
                'distance' => $data['rows'][0]['elements'][0]['distance']['value'],
                'duration' => $data['rows'][0]['elements'][0]['duration']['value']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function calculateFinalFare($package, $distanceKm, $durationMinutes)
    {
        $baseFare = $package->base_price;
        $extraKm = max(0, $distanceKm - $package->base_distance);
        $extraHours = max(0, ($durationMinutes / 60) - $package->base_hours);

        $extraKmCharge = $extraKm * $package->extra_km_rate;
        $extraHourCharge = $extraHours * $package->extra_hour_rate;

        return [
            'base_fare' => $baseFare,
            'extra_km' => round($extraKm, 2),
            'extra_km_charge' => round($extraKmCharge, 2),
            'extra_hours' => round($extraHours, 2),
            'extra_hour_charge' => round($extraHourCharge, 2),
            'total_fare' => round($baseFare + $extraKmCharge + $extraHourCharge, 2),
            'package_name' => $package->name
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required',
            'drop_location' => 'required',
            'package_id' => 'required|exists:taxi_packages,id',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
            'total_fare' => 'required|numeric',
            'base_fare' => 'required|numeric',
            'extra_km_charge' => 'required|numeric',
            'extra_hour_charge' => 'required|numeric',
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'package_id' => $request->package_id,
            'pickup_location' => $request->pickup_location,
            'drop_location' => $request->drop_location,
            'actual_distance' => $request->distance,
            'actual_duration' => $request->duration,
            'base_fare' => $request->base_fare,
            'extra_km_charge' => $request->extra_km_charge,
            'extra_hour_charge' => $request->extra_hour_charge,
            'total_fare' => $request->total_fare,
            'booking_status' => 'confirmed'
        ]);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'message' => 'Booking confirmed successfully!'
        ]);
    }

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->with('package')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }
}
