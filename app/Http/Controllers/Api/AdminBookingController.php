<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    // Simple safety: only allow in local environment or when APP_DEBUG is true
    protected function ensureAllowed()
    {
        if (!app()->isLocal() && !config('app.debug')) {
            abort(403, 'Not allowed in this environment');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAllowed();
        $bookings = Booking::with(['car.model.comfortCategory','car.driver','user'])->orderBy('starts_at')->get();
        return response()->json(['data' => $bookings]);
    }

    public function userBookings(Request $request)
    {
        $this->ensureAllowed();
        $userEmail = $request->query('email');
        
        if (!$userEmail) {
            return response()->json(['data' => []]);
        }

        // Debug: проверяем существует ли пользователь
        $user = \App\Models\User::where('email', $userEmail)->first();
        if (!$user) {
            return response()->json([
                'data' => [],
                'debug' => ['error' => 'User not found', 'email' => $userEmail]
            ]);
        }

        $bookings = Booking::with(['car.model.comfortCategory','car.driver','user.position'])
            ->where('user_id', $user->id)  // Используем прямое сравнение user_id
            ->orderBy('starts_at')
            ->get();

        // Debug информация
        $debug = [
            'user_email' => $userEmail,
            'user_id' => $user->id,
            'bookings_count' => $bookings->count(),
            'all_bookings_count' => Booking::count(),
        ];

        // Форматируем данные как в основном BookingController
        $formatted = $bookings->map(function (Booking $b) {
            return [
                'id' => $b->id,
                'starts_at' => $b->starts_at->toDateTimeString(),
                'ends_at'   => $b->ends_at->toDateTimeString(),
                'status'    => $b->status,
                'purpose'   => $b->purpose,
                'user' => [
                    'id' => $b->user->id,
                    'name' => $b->user->name,
                    'email' => $b->user->email,
                    'position' => $b->user->position ? $b->user->position->name : 'Не указана',
                ],
                'car' => [
                    'id' => $b->car->id,
                    'license_plate' => $b->car->license_plate,
                    'vin' => $b->car->vin,
                    'model' => [
                        'id' => $b->car->model->id,
                        'brand' => $b->car->model->brand,
                        'model' => $b->car->model->model,
                        'category' => [
                            'id' => $b->car->model->comfortCategory->id,
                            'name' => $b->car->model->comfortCategory->name,
                        ],
                    ],
                    'driver' => [
                        'id' => $b->car->driver->id,
                        'full_name' => $b->car->driver->full_name,
                        'phone' => $b->car->driver->phone,
                    ],
                ],
            ];
        });

        return response()->json([
            'data' => $formatted,
            'debug' => $debug
        ]);
    }

    public function destroy($id, Request $request)
    {
        $this->ensureAllowed();
        $b = Booking::find($id);
        if (!$b) return response()->json(['message' => 'Not found'], 404);
        $b->delete();
        return response()->noContent();
    }

    public function destroyAll(Request $request)
    {
        $this->ensureAllowed();
        Booking::query()->delete();
        return response()->noContent();
    }
}
