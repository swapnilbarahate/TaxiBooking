@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card booking-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Book a Taxi</h5>
            </div>

            <div class="card-body">
                <form id="booking-form">
                    @csrf

                    <div class="mb-3">
                        <label for="pickup_location" class="form-label">Pickup Location</label>
                        <input type="text" class="form-control" id="pickup_location" name="pickup_location" placeholder="Enter pickup address" required>
                        <div class="form-text">Start typing to see suggestions</div>
                    </div>

                    <div class="mb-3">
                        <label for="drop_location" class="form-label">Drop Location</label>
                        <input type="text" class="form-control" id="drop_location" name="drop_location" placeholder="Enter drop address" required>
                        <div class="form-text">Start typing to see suggestions</div>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="package_id" class="form-label">Select Package</label>
                        <select class="form-select" id="package_id" name="package_id" required>
                            <option value="">-- Select Package --</option>
                            @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} (₹{{ $package->base_price }})</option>
                            @endforeach
                        </select>
                    </div> -->

                    <div class="mb-3">
                        <label for="package_id" class="form-label">Select Package</label>
                        <select class="form-select" id="package_id" name="package_id" required>
                            <option value="">-- Select Package --</option>
                            @foreach($packages as $package)
                            <option value="{{ $package->id }}" 
                                    data-distance="{{ $package->base_distance }}"
                                    data-hours="{{ $package->base_hours }}"
                                    data-price="{{ $package->base_price }}"
                                    data-extra-km="{{ $package->extra_km_rate }}"
                                    data-extra-hour="{{ $package->extra_hour_rate }}" 
                                    title="Includes {{ $package->base_distance }} km, Extra: ₹{{ $package->extra_hour_rate }}/hrs, ₹{{ $package->extra_km_rate }}/km">
                                    {{ $package->name }}: ₹{{ $package->base_price }}
                            </option>
                            @endforeach
                        </select>
                        <div id="package-suggestions" class="mt-2"></div>
                    </div>

                    <button type="button" id="calculate-fare" class="btn btn-primary w-100 py-2">
                        Calculate Fare
                    </button>
                </form>

                <div id="fare-breakdown" class="fare-breakdown d-none">
                    <h5 class="text-center mb-4">Fare Breakdown</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Package:</strong> <span id="package-name" class="float-end"></span></p>
                            <p><strong>Distance:</strong> <span id="distance" class="float-end">0</span> km</p>
                            <p><strong>Duration:</strong> <span id="duration" class="float-end">0</span> mins</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Base Fare:</strong> ₹<span id="base-fare" class="float-end">0</span></p>
                            <p><strong>Extra KM Charge:</strong> ₹<span id="extra-km-charge" class="float-end">0</span></p>
                            <p><strong>Extra Hour Charge:</strong> ₹<span id="extra-hour-charge" class="float-end">0</span></p>
                            <hr>
                            <p class="fw-bold fs-5">Total Fare: ₹<span id="total-fare" class="float-end">0</span></p>
                        </div>
                    </div>
                    <button id="confirm-booking" class="btn btn-success w-100 mt-3 py-2">
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
@endpush

@push('scripts')
<script>

    function initAutocomplete() {
        const pickupInput = document.getElementById('pickup_location');
        const dropInput = document.getElementById('drop_location');
        
        if (!window.google || !window.google.maps || !window.google.maps.places) {
            Swal.fire({
                icon: 'error',
                title: 'Location Service Error',
                text: 'Google Maps API failed to load. Please refresh the page.',
            });
            return;
        }
        
        const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput, {
            types: ['geocode'],
            componentRestrictions: {country: 'in'},
            fields: ['formatted_address', 'geometry']
        });
        
        const dropAutocomplete = new google.maps.places.Autocomplete(dropInput, {
            types: ['geocode'],
            componentRestrictions: {country: 'in'},
            fields: ['formatted_address', 'geometry']
        });

        // Add place_changed listener to trigger package suggestions
        pickupAutocomplete.addListener('place_changed', function() {
            const place = pickupAutocomplete.getPlace();
            if (!place.geometry) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Location',
                    text: 'Please select a valid location from the suggestions',
                }).then(() => {
                    pickupInput.value = '';
                    pickupInput.focus();
                });
            } else {
                // Manually trigger the change event after selection
                $(pickupInput).trigger('change');
            }
        });

        dropAutocomplete.addListener('place_changed', function() {
            const place = dropAutocomplete.getPlace();
            if (!place.geometry) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Location',
                    text: 'Please select a valid location from the suggestions',
                }).then(() => {
                    dropInput.value = '';
                    dropInput.focus();
                });
            } else {
                // Manually trigger the change event after selection
                        $(dropInput).trigger('change');
                    }
                });
    }

    $(document).ready(function() {
        initAutocomplete();

        $('#pickup_location, #drop_location').on('change', function() {
            const pickup = $('#pickup_location').val();
            const drop = $('#drop_location').val();
            
            if (pickup && drop) {
                // Show loading indicator
                $('#package-suggestions').html('<div class="text-center my-2"><div class="spinner-border text-primary" role="status"></div></div>');
                
                setTimeout(function() {
                    $.ajax({
                        url: '/suggest-packages',
                        method: 'POST',
                        data: {
                            pickup: $('#pickup_location').val(), 
                            drop: $('#drop_location').val(),    
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                let suggestionsHtml = `
                                    <div class="alert alert-info p-2 mb-2">
                                        <strong>Route Distance:</strong> ${response.distance} km<br>
                                        <strong>Estimated Duration:</strong> ${response.duration} hrs
                                    </div>`;
                                
                                if (response.packages.length > 0) {
                                    suggestionsHtml += `
                                    <div class="alert alert-success p-2">
                                        <strong class="mb-2 d-block">Recommended Packages:</strong>`;
                                    
                                    response.packages.forEach(pkg => {
                                        // Calculate if this package covers the distance/duration
                                        const coversDistance = pkg.base_distance >= response.distance;
                                        const coversDuration = pkg.base_hours >= response.duration;
                                        
                                        suggestionsHtml += `
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                name="suggested_package" 
                                                id="suggested-${pkg.id}" 
                                                value="${pkg.id}"
                                                onclick="$('#package_id').val('${pkg.id}')">
                                            <label class="form-check-label" for="suggested-${pkg.id}">
                                                <strong>${pkg.name}</strong><br>
                                                <small>
                                                    ${pkg.base_distance} km / ${pkg.base_hours} hrs included<br>
                                                    Base Price: ₹${pkg.base_price}<br>
                                                    Extra: ₹${pkg.extra_km_rate}/km, ₹${pkg.extra_hour_rate}/hr
                                                </small>
                                                ${coversDistance && coversDuration ? 
                                                    '<span class="badge bg-success ms-2">Perfect Match</span>' : 
                                                    '<span class="badge bg-warning text-dark ms-2">Partial Match</span>'}
                                            </label>
                                        </div>`;
                                    });
                                    
                                    suggestionsHtml += `</div>`;
                                } else {
                                    suggestionsHtml += `
                                    <div class="alert alert-warning p-2">
                                        No package fully covers this route. The On-Demand package will be used.
                                    </div>`;
                                }
                                
                                $('#package-suggestions').html(suggestionsHtml);
                            }
                        },
                        error: function(xhr) {
                            $('#package-suggestions').html(`
                                <div class="alert alert-danger p-2">
                                    Could not suggest packages. Please select one manually.
                                </div>`);
                            console.error(xhr.responseText);
                        }
                    });
                }, 300); 
            }
        });
        
        $('#calculate-fare').click(function() {
            const pickup = $('#pickup_location').val();
            const drop = $('#drop_location').val();
            const packageId = $('#package_id').val();

            if (!pickup || !drop || !packageId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill all fields before calculating fare',
                });
                return;
            }

            const $btn = $(this);
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Calculating...');
            $btn.prop('disabled', true);
            
            $.ajax({
                url: '/calculate-fare',
                method: 'POST',
                data: {
                    pickup: pickup,
                    drop: drop,
                    package_id: packageId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $btn.html('Calculate Fare');
                    $btn.prop('disabled', false);
                    
                    if (response.success) {
                        $('#package-name').text(response.fare.package_name);
                        $('#distance').text(response.distance);
                        $('#duration').text(response.duration);
                        $('#base-fare').text(response.fare.base_fare);
                        $('#extra-km-charge').text(response.fare.extra_km_charge);
                        $('#extra-hour-charge').text(response.fare.extra_hour_charge);
                        $('#total-fare').text(response.fare.total_fare);
                        
                        $('#fare-breakdown').removeClass('d-none');
                        
                        $('html, body').animate({
                            scrollTop: $('#fare-breakdown').offset().top - 100
                        }, 500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Calculation Error',
                            text: response.error || 'Failed to calculate fare',
                        });
                    }
                },
                error: function(xhr) {
                    $btn.html('Calculate Fare');
                    $btn.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Error calculating fare. Please try again.',
                    });
                    console.error(xhr.responseText);
                }
            });
        });

        $('#confirm-booking').click(function() {
            const pickup = $('#pickup_location').val();
            const drop = $('#drop_location').val();
            const packageId = $('#package_id').val();
            const distance = $('#distance').text();
            const duration = $('#duration').text();
            const baseFare = $('#base-fare').text();
            const extraKmCharge = $('#extra-km-charge').text();
            const extraHourCharge = $('#extra-hour-charge').text();
            const totalFare = $('#total-fare').text();

            const $btn = $(this);
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...');
            $btn.prop('disabled', true);
            
            $.ajax({
                url: '/book-taxi',
                method: 'POST',
                data: {
                    pickup_location: pickup,
                    drop_location: drop,
                    package_id: packageId,
                    distance: distance,
                    duration: duration,
                    base_fare: baseFare,
                    extra_km_charge: extraKmCharge,
                    extra_hour_charge: extraHourCharge,
                    total_fare: totalFare,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $btn.html('Confirm Booking');
                    $btn.prop('disabled', false);
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Confirmed!',
                            html: `Your booking ID is: <strong>${response.booking_id}</strong><br>
                                   Driver will contact you shortly.`,
                            confirmButtonText: 'View Booking',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/my-bookings';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Failed',
                            text: response.message || 'Failed to confirm booking',
                        });
                    }
                },
                error: function(xhr) {
                    $btn.html('Confirm Booking');
                    $btn.prop('disabled', false);
                    
                    let errorMsg = 'Error confirming booking. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Error',
                        text: errorMsg,
                    });
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endpush
@endsection