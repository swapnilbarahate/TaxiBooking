@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card booking-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">My Bookings</h5>
            </div>

            <div class="card-body">
                @if($bookings->isEmpty())
                    <div class="text-center py-4">
                        <h4>You have no bookings yet</h4>
                        <p class="text-muted">Book your first taxi to see it here</p>
                        <a href="{{ url('/') }}" class="btn btn-primary mt-2">Book a Taxi</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Package</th>
                                    <th>Pickup</th>
                                    <th>Drop</th>
                                    <th>Distance</th>
                                    <th>Duration</th>
                                    <th>Total Fare</th>
                                    <th>Status</th>
                                    <th>Booked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->package->name }}</td>
                                    <td>{{ Str::limit($booking->pickup_location, 15) }}</td>
                                    <td>{{ Str::limit($booking->drop_location, 15) }}</td>
                                    <td>{{ $booking->actual_distance }} km</td>
                                    <td>{{ $booking->actual_duration }} mins</td>
                                    <td>₹{{ $booking->total_fare }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->booking_status == 'confirmed' ? 'success' : ($booking->booking_status == 'completed' ? 'info' : 'warning') }}">
                                            {{ ucfirst($booking->booking_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection