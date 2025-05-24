<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('package_id')->constrained('taxi_packages');
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->decimal('actual_distance', 8, 2);
            $table->decimal('actual_duration', 8, 2);
            $table->decimal('base_fare', 10, 2);
            $table->decimal('extra_km_charge', 10, 2)->default(0);
            $table->decimal('extra_hour_charge', 10, 2)->default(0);
            $table->decimal('total_fare', 10, 2);
            $table->string('booking_status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
