<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_model_id')->constrained('car_models');
            $table->string('license_plate')->unique();
            $table->string('vin')->nullable()->unique();
            $table->foreignId('driver_id')->constrained('drivers');
            $table->timestamps();

            $table->index('car_model_id');
            $table->index('driver_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cars');
    }
};
