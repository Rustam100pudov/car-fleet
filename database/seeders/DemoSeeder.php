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
        $posDir = Position::firstOrCreate(['name' => 'Director']);
        $posTest = Position::firstOrCreate(['name' => 'Test']); // Добавляем позицию Test

        $cat1 = ComfortCategory::firstOrCreate(['name' => 'Первая','rank'=>1]);
        $cat2 = ComfortCategory::firstOrCreate(['name' => 'Вторая','rank'=>2]);
        $cat3 = ComfortCategory::firstOrCreate(['name' => 'Третья','rank'=>3]);

        // Связываем должности с категориями комфорта
        $posMgr->comfortCategories()->syncWithoutDetaching([$cat1->id, $cat2->id]);
        $posEng->comfortCategories()->syncWithoutDetaching([$cat2->id, $cat3->id]);
        $posDir->comfortCategories()->syncWithoutDetaching([$cat1->id, $cat2->id, $cat3->id]);
        $posTest->comfortCategories()->syncWithoutDetaching([$cat3->id]); // Test только 3-я категория

        // Модели автомобилей
        $m1 = CarModel::firstOrCreate(['brand'=>'Toyota','model'=>'Camry','comfort_category_id'=>$cat1->id]);
        $m2 = CarModel::firstOrCreate(['brand'=>'Skoda','model'=>'Octavia','comfort_category_id'=>$cat2->id]);
        $m3 = CarModel::firstOrCreate(['brand'=>'Toyota','model'=>'Corolla','comfort_category_id'=>$cat1->id]);
        $m4 = CarModel::firstOrCreate(['brand'=>'Nissan','model'=>'Qashqai','comfort_category_id'=>$cat2->id]);
        $m5 = CarModel::firstOrCreate(['brand'=>'Skoda','model'=>'Superb','comfort_category_id'=>$cat2->id]);
        $m6 = CarModel::firstOrCreate(['brand'=>'BMW','model'=>'X5','comfort_category_id'=>$cat1->id]);
        $m7 = CarModel::firstOrCreate(['brand'=>'Mercedes','model'=>'E-Class','comfort_category_id'=>$cat1->id]);
        $m8 = CarModel::firstOrCreate(['brand'=>'Volkswagen','model'=>'Polo','comfort_category_id'=>$cat3->id]);
        $m9 = CarModel::firstOrCreate(['brand'=>'Hyundai','model'=>'Solaris','comfort_category_id'=>$cat3->id]);

        // Водители
        $d1 = Driver::firstOrCreate(['full_name'=>'Водитель А','phone'=>'+7 900 000-00-01']);
        $d2 = Driver::firstOrCreate(['full_name'=>'Водитель Б','phone'=>'+7 900 000-00-02']);
        $d3 = Driver::firstOrCreate(['full_name'=>'Водитель В','phone'=>'+7 900 000-00-03']);
        $d4 = Driver::firstOrCreate(['full_name'=>'Водитель Г','phone'=>'+7 900 000-00-04']);
        $d5 = Driver::firstOrCreate(['full_name'=>'Водитель Д','phone'=>'+7 900 000-00-05']);
        $d6 = Driver::firstOrCreate(['full_name'=>'Водитель Е','phone'=>'+7 900 000-00-06']);
        $d7 = Driver::firstOrCreate(['full_name'=>'Водитель Ж','phone'=>'+7 900 000-00-07']);
        $d8 = Driver::firstOrCreate(['full_name'=>'Водитель З','phone'=>'+7 900 000-00-08']);
        $d9 = Driver::firstOrCreate(['full_name'=>'Водитель И','phone'=>'+7 900 000-00-09']);

        // Автомобили
        Car::firstOrCreate(['license_plate'=>'A001AA116'], ['car_model_id'=>$m1->id,'driver_id'=>$d1->id]);
        Car::firstOrCreate(['license_plate'=>'A002AA116'], ['car_model_id'=>$m2->id,'driver_id'=>$d2->id]);
        Car::firstOrCreate(['license_plate'=>'A003AA116'], ['car_model_id'=>$m3->id,'driver_id'=>$d3->id]);
        Car::firstOrCreate(['license_plate'=>'A004AA116'], ['car_model_id'=>$m4->id,'driver_id'=>$d4->id]);
        Car::firstOrCreate(['license_plate'=>'A005AA116'], ['car_model_id'=>$m5->id,'driver_id'=>$d5->id]);
        Car::firstOrCreate(['license_plate'=>'B001BB116'], ['car_model_id'=>$m6->id,'driver_id'=>$d6->id]);
        Car::firstOrCreate(['license_plate'=>'B002BB116'], ['car_model_id'=>$m7->id,'driver_id'=>$d7->id]);
        Car::firstOrCreate(['license_plate'=>'C001CC116'], ['car_model_id'=>$m8->id,'driver_id'=>$d8->id]);
        Car::firstOrCreate(['license_plate'=>'C002CC116'], ['car_model_id'=>$m9->id,'driver_id'=>$d9->id]);
    }
}
