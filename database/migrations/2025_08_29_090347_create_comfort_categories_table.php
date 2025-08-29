<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comfort_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('rank')->index();
            $table->timestamps();
        });

        Schema::create('position_category', function (Blueprint $table) {
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('comfort_category_id')->constrained('comfort_categories')->cascadeOnDelete();
            $table->primary(['position_id', 'comfort_category_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('position_category');
        Schema::dropIfExists('comfort_categories');
    }
};
