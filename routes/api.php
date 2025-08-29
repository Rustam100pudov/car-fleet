<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AvailableCarsController;
use App\Http\Controllers\Api\BookingController; // ← добавили
use App\Http\Controllers\Api\AdminBookingController; // admin helper
use App\Models\User;
use App\Models\Position;

// Dev: выдача токена (оставь для локальной разработки)
Route::get('/dev/make-token', function () {
    // optional: ?email=manager@example.com to get token for specific demo user
    $email = request()->query('email', 'demo@example.com');
    $posName = request()->query('position', 'Manager');

    $pos = Position::firstOrCreate(['name' => $posName]);

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name'        => explode('@', $email)[0],
            'password'    => bcrypt('password'),
            'position_id' => $pos->id,
        ]
    );

    return response()->json([
        'token' => $user->createToken('demo')->plainTextToken,
        'user'  => $user->only(['id', 'name', 'email','position_id']),
    ]);
});

// Все API под защитой Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Свободные машины
    Route::get('/available-cars', [AvailableCarsController::class, 'index']);

    // Бронирования
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

    // Admin helpers for local demo: list/delete all bookings
    Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
    Route::delete('/admin/bookings/{id}', [AdminBookingController::class, 'destroy']);
    Route::delete('/admin/bookings', [AdminBookingController::class, 'destroyAll']);
});
