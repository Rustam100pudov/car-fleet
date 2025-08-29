<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Position, ComfortCategory, CarModel, Driver, Car, Booking};
use Carbon\Carbon;

class AvailableCarsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_booked_car_is_excluded_from_available_list()
    {
        // setup data
        $pos = Position::create(['name' => 'Manager']);
        $cat1 = ComfortCategory::create(['name' => 'Первая', 'rank' => 1]);
        $cat2 = ComfortCategory::create(['name' => 'Вторая', 'rank' => 2]);
        $pos->comfortCategories()->attach([$cat1->id, $cat2->id]);

        $model1 = CarModel::create(['brand' => 'Toyota','model' => 'Camry','comfort_category_id' => $cat1->id]);
        $model2 = CarModel::create(['brand' => 'Skoda','model' => 'Octavia','comfort_category_id' => $cat2->id]);

        $driver = Driver::create(['full_name' => 'Иванов','phone' => '+70000000001']);

        $car1 = Car::create(['car_model_id' => $model1->id, 'license_plate' => 'A001AA116','driver_id' => $driver->id]);
        $car2 = Car::create(['car_model_id' => $model2->id, 'license_plate' => 'A002AA116','driver_id' => $driver->id]);

        $user = User::factory()->create(['position_id' => $pos->id]);

        $token = $user->createToken('test')->plainTextToken;

        // create a booking that overlaps with interval for car1
        Booking::create([
            'car_id' => $car1->id,
            'user_id' => $user->id,
            'starts_at' => Carbon::parse('2025-09-01 09:30:00'),
            'ends_at'   => Carbon::parse('2025-09-01 11:00:00'),
            'status' => 'approved',
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/available-cars?start=2025-09-01T09:00:00&end=2025-09-01T12:00:00');

        $response->assertStatus(200);

        $data = $response->json('data');
        // data может быть paginator-ом (с ключом 'data') или просто массивом
        if (is_array($data) && array_key_exists('data', $data)) {
            $items = $data['data'];
        } else {
            $items = $data;
        }

        $ids = array_column($items, 'id');

        $this->assertNotContains($car1->id, $ids, 'Занятая машина должна отсутствовать в списке');
        $this->assertContains($car2->id, $ids, 'Свободная машина должна присутствовать в списке');
    }
}
