<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->foreignId('comfort_category_id')->constrained('comfort_categories');
            $table->timestamps();
            $table->unique(['brand','model']);
            $table->index('comfort_category_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('car_models');
    }
};
