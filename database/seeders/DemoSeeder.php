<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Position,ComfortCategory,CarModel,Driver,Car};

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $posMgr = Position::firstOrCreate(['name' => 'Manager']);
        $posEng = Position::firstOrCreate(['name' => 'Engineer']);

        $cat1 = ComfortCategory::firstOrCreate(['name' => 'Первая','rank'=>1]);
        $cat2 = ComfortCategory::firstOrCreate(['name' => 'Вторая','rank'=>2]);

        $posMgr->comfortCategories()->syncWithoutDetaching([$cat1->id, $cat2->id]);
        $posEng->comfortCategories()->syncWithoutDetaching([$cat2->id]);

        $m1 = CarModel::firstOrCreate(['brand'=>'Toyota','model'=>'Camry','comfort_category_id'=>$cat1->id]);
        $m2 = CarModel::firstOrCreate(['brand'=>'Skoda','model'=>'Octavia','comfort_category_id'=>$cat2->id]);

        $d1 = Driver::firstOrCreate(['full_name'=>'Иванов Иван','phone'=>'+7 900 000-00-01']);
        $d2 = Driver::firstOrCreate(['full_name'=>'Петров Пётр','phone'=>'+7 900 000-00-02']);

    // additional drivers
    $d3 = Driver::firstOrCreate(['full_name'=>'Сидоров Сергей','phone'=>'+7 900 000-00-03']);
    $d4 = Driver::firstOrCreate(['full_name'=>'Кузнецов Алексей','phone'=>'+7 900 000-00-04']);
    $d5 = Driver::firstOrCreate(['full_name'=>'Морозова Анна','phone'=>'+7 900 000-00-05']);

    Car::firstOrCreate(['license_plate'=>'A001AA116'], ['car_model_id'=>$m1->id,'driver_id'=>$d1->id]);
    Car::firstOrCreate(['license_plate'=>'A002AA116'], ['car_model_id'=>$m2->id,'driver_id'=>$d2->id]);

    // more car models and cars for demo
    $m3 = CarModel::firstOrCreate(['brand'=>'Toyota','model'=>'Corolla','comfort_category_id'=>$cat1->id]);
    $m4 = CarModel::firstOrCreate(['brand'=>'Nissan','model'=>'Qashqai','comfort_category_id'=>$cat2->id]);
    $m5 = CarModel::firstOrCreate(['brand'=>'Skoda','model'=>'Superb','comfort_category_id'=>$cat2->id]);

    Car::firstOrCreate(['license_plate'=>'A003AA116'], ['car_model_id'=>$m3->id,'driver_id'=>$d3->id]);
    Car::firstOrCreate(['license_plate'=>'A004AA116'], ['car_model_id'=>$m4->id,'driver_id'=>$d4->id]);
    Car::firstOrCreate(['license_plate'=>'A005AA116'], ['car_model_id'=>$m5->id,'driver_id'=>$d5->id]);
    }
}
