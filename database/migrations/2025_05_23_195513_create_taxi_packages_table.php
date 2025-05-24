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
        Schema::create('taxi_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('base_distance', 8, 2);
            $table->decimal('base_hours', 8, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('extra_km_rate', 8, 2);
            $table->decimal('extra_hour_rate', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxi_packages');
    }
};
