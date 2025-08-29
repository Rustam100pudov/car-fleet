<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AvailableCarsController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'start'       => ['required','date'],
            'end'         => ['required','date','after:start'],
            'model_id'    => ['nullable','integer','exists:car_models,id'],
            'category_id' => ['nullable','integer','exists:comfort_categories,id'],
            'brand'       => ['nullable','string'],
            'driver_id'   => ['nullable','integer','exists:drivers,id'],
        ]);

        $user = Auth::user();
        abort_unless($user && $user->position, 403, 'У пользователя не задана должность');

        $allowedCategoryIds = $user->position
            ->comfortCategories()
            ->pluck('comfort_categories.id');

    // no debug output in production demo responses

        $q = Car::query()
            ->with(['model.comfortCategory','driver'])
            ->whereHas('model', fn (Builder $m) =>
                $m->whereIn('comfort_category_id', $allowedCategoryIds)
            )
            ->availableBetween(new \DateTimeImmutable($data['start']), new \DateTimeImmutable($data['end']));

        if (!empty($data['model_id'])) {
            $q->where('car_model_id', $data['model_id']);
        }
        if (!empty($data['category_id'])) {
            $q->whereHas('model', fn (Builder $m) => $m->where('comfort_category_id', $data['category_id']));
        }
        if (!empty($data['brand'])) {
            $q->whereHas('model', fn (Builder $m) => $m->where('brand', 'like', $data['brand'] . '%'));
        }
        if (!empty($data['driver_id'])) {
            $q->where('driver_id', $data['driver_id']);
        }

        $cars = $q->orderBy('id')->paginate(20);

    $resp = [
            'data' => $cars->through(function (Car $car) {
                return [
                    'id'            => $car->id,
                    'license_plate' => $car->license_plate,
                    'vin'           => $car->vin,
                    'model' => [
                        'id'      => $car->model->id,
                        'brand'   => $car->model->brand,
                        'model'   => $car->model->model,
                        'category'=> [
                            'id'   => $car->model->comfortCategory->id,
                            'name' => $car->model->comfortCategory->name,
                            'rank' => $car->model->comfortCategory->rank,
                        ],
                    ],
                    'driver' => [
                        'id'        => $car->driver->id,
                        'full_name' => $car->driver->full_name,
                        'phone'     => $car->driver->phone,
                    ],
                ];
            }),
            'meta' => [
                'current_page' => $cars->currentPage(),
                'last_page'    => $cars->lastPage(),
                'total'        => $cars->total(),
            ],
    ];

    return response()->json($resp);
    }
}
