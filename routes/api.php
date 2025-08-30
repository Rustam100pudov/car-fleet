<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AvailableCarsController;
use App\Http\Controllers\Api\BookingController; // ← добавили
use App\Http\Controllers\Api\AdminBookingController; // admin helper
use App\Models\User;
use App\Models\Position;
use App\Models\ComfortCategory;

// Dev: выдача токена (оставь для локальной разработки)
Route::get('/dev/make-token', function () {
    // optional: ?email=manager@example.com to get token for specific demo user
    $email = request()->query('email', 'demo@example.com');
    
    \Log::info('Token request', ['email' => $email]);

    // Ищем существующего пользователя по email
    $user = User::where('email', $email)->first();

    if (!$user) {
        \Log::info('User not found, creating new user', ['email' => $email]);
        // Определяем позицию по email
        $positionName = 'Manager'; // по умолчанию
        if (str_contains($email, 'engineer')) {
            $positionName = 'Engineer';
        } elseif (str_contains($email, 'director')) {
            $positionName = 'Director';
        } elseif (str_contains($email, 'manager')) {
            $positionName = 'Manager';
        } elseif (str_contains($email, 'test')) {
            $positionName = 'Test';
        }

        $position = Position::where('name', $positionName)->first();
        if (!$position) {
            $position = Position::firstOrCreate(['name' => $positionName]);
            
            // Устанавливаем связи с категориями комфорта в зависимости от позиции
            $cat1 = ComfortCategory::where('name', 'Первая')->first();
            $cat2 = ComfortCategory::where('name', 'Вторая')->first();
            $cat3 = ComfortCategory::where('name', 'Третья')->first();
            
            if ($positionName === 'Manager' && $cat1 && $cat2) {
                $position->comfortCategories()->syncWithoutDetaching([$cat1->id, $cat2->id]);
            } elseif ($positionName === 'Engineer' && $cat2 && $cat3) {
                $position->comfortCategories()->syncWithoutDetaching([$cat2->id, $cat3->id]);
            } elseif ($positionName === 'Director' && $cat1 && $cat2 && $cat3) {
                $position->comfortCategories()->syncWithoutDetaching([$cat1->id, $cat2->id, $cat3->id]);
            } elseif ($positionName === 'Test' && $cat3) {
                $position->comfortCategories()->syncWithoutDetaching([$cat3->id]);
            }
        }

        $user = User::create([
            'name'        => explode('@', $email)[0],
            'email'       => $email,
            'password'    => bcrypt('password'),
            'position_id' => $position->id,
        ]);
    } else {
        \Log::info('User found', [
            'email' => $user->email, 
            'position' => $user->position ? $user->position->name : 'no position'
        ]);
    }

    // Очищаем старые токены для этого пользователя
    $oldTokensCount = $user->tokens()->count();
    $user->tokens()->delete();
    \Log::info('Cleared old tokens', ['count' => $oldTokensCount]);

    $token = $user->createToken('demo')->plainTextToken;
    \Log::info('Token created', ['user_id' => $user->id, 'token_length' => strlen($token)]);

    return response()->json([
        'token' => $token,
        'user'  => $user->only(['id', 'name', 'email','position_id']),
        'user_id' => $user->id,
        'debug' => [
            'requested_email' => $email,
            'found_user' => $user->email,
            'user_position' => $user->position ? $user->position->name : 'no position',
            'token_created' => now()->toDateTimeString()
        ]
    ]);
});

// Debug endpoint для диагностики
Route::get('/debug/bookings', function () {
    $bookings = \App\Models\Booking::with('user', 'car')->get();
    return response()->json([
        'bookings' => $bookings->map(function($b) {
            return [
                'id' => $b->id,
                'user_email' => $b->user->email,
                'car_plate' => $b->car->license_plate,
                'starts_at' => $b->starts_at,
                'ends_at' => $b->ends_at,
            ];
        }),
        'count' => $bookings->count()
    ]);
});

Route::get('/debug/users', function () {
    $users = \App\Models\User::with('position')->get();
    return response()->json([
        'users' => $users->map(function($u) {
            return [
                'id' => $u->id,
                'email' => $u->email,
                'position' => $u->position ? $u->position->name : 'no position',
            ];
        })
    ]);
});

Route::get('/debug/logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return response()->json(['logs' => 'Log file not found']);
    }
    
    $logs = file_get_contents($logFile);
    // Берем последние 5000 символов
    $logs = substr($logs, -5000);
    
    return response()->json(['logs' => $logs]);
});

Route::get('/debug/positions', function () {
    $positions = \App\Models\Position::with('comfortCategories')->get();
    return response()->json([
        'positions' => $positions->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'categories' => $p->comfortCategories->map(function($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'rank' => $c->rank
                    ];
                })
            ];
        })
    ]);
});

Route::get('/debug/cars', function () {
    $cars = \App\Models\Car::with('model.comfortCategory')->get();
    return response()->json([
        'cars' => $cars->map(function($c) {
            return [
                'id' => $c->id,
                'license_plate' => $c->license_plate,
                'model' => $c->model->brand . ' ' . $c->model->model,
                'category' => $c->model->comfortCategory ? [
                    'id' => $c->model->comfortCategory->id,
                    'name' => $c->model->comfortCategory->name,
                    'rank' => $c->model->comfortCategory->rank
                ] : null
            ];
        })
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
    Route::get('/admin/user-bookings', [AdminBookingController::class, 'userBookings']);
    Route::delete('/admin/bookings/{id}', [AdminBookingController::class, 'destroy']);
    Route::delete('/admin/bookings', [AdminBookingController::class, 'destroyAll']);
});
