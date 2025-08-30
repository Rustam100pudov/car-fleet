<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Список бронирований текущего пользователя
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 20);

        $bookings = Booking::with(['car.model.comfortCategory', 'car.driver', 'user.position'])
            ->where('user_id', $request->user()->id)
            ->orderBy('starts_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $bookings->through(function (Booking $b) {
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
            }),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'total'        => $bookings->total(),
            ],
        ]);
    }

    // Создание брони
    public function store(Request $request)
    {
        // Debug: логируем информацию о пользователе
        \Log::info('Booking store attempt', [
            'user_id' => $request->user()->id,
            'user_email' => $request->user()->email,
            'user_position' => $request->user()->position ? $request->user()->position->name : 'no position',
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'car_id'  => ['required', 'exists:cars,id'],
            'start'   => ['required', 'date'],
            'end'     => ['required', 'date', 'after:start'],
            'purpose' => ['nullable', 'string', 'max:255'],
        ]);

        // Проверяем, доступна ли машина для пользователя
        $car = \App\Models\Car::with('model.comfortCategory')->find($validated['car_id']);
        $userCategoryIds = $request->user()->position->comfortCategories()->pluck('comfort_categories.id');
        
        \Log::info('Car access check', [
            'car_id' => $car->id,
            'car_category_id' => $car->model->comfortCategory->id,
            'user_allowed_categories' => $userCategoryIds->toArray(),
            'access_granted' => $userCategoryIds->contains($car->model->comfortCategory->id)
        ]);

        if (!$userCategoryIds->contains($car->model->comfortCategory->id)) {
            \Log::warning('User attempted to book car from unauthorized category', [
                'user_id' => $request->user()->id,
                'car_id' => $car->id,
                'car_category' => $car->model->comfortCategory->name
            ]);
            return response()->json([
                'message' => 'У вас нет доступа к машинам этой категории.',
                'errors'  => ['car_id' => ['unauthorized_category']],
            ], 403);
        }

        $start = Carbon::parse($validated['start']);
        $end   = Carbon::parse($validated['end']);

        // Проверка пересечения интервалов:
        // существует ли запись, где (ends_at > start) И (starts_at < end)
        $overlap = Booking::where('car_id', $validated['car_id'])
            ->where('ends_at', '>', $start)
            ->where('starts_at', '<', $end)
            ->exists();

        if ($overlap) {
            \Log::info('Booking overlap detected', [
                'car_id' => $validated['car_id'],
                'start' => $start,
                'end' => $end
            ]);
            return response()->json([
                'message' => 'Машина уже занята в указанный интервал.',
                'errors'  => ['interval' => ['overlap']],
            ], 422);
        }

        $booking = Booking::create([
            'car_id'    => $validated['car_id'],
            'user_id'   => $request->user()->id,
            'starts_at' => $start,
            'ends_at'   => $end,
            'purpose'   => $validated['purpose'] ?? null,
        ]);

        \Log::info('Booking created successfully', [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'car_id' => $booking->car_id,
            'starts_at' => $booking->starts_at,
            'ends_at' => $booking->ends_at
        ]);

        $booking->load('car.model.comfortCategory', 'car.driver', 'user.position');

        // return consistent shape
        $b = $booking;
        $payload = [
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

        return response()->json(['data' => $payload], 201);
    }

    // Отмена своей брони
    public function destroy(Booking $booking, Request $request)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }
        $booking->delete();
        return response()->noContent();
    }
}
