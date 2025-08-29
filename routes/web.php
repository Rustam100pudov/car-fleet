<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Demo admin page to test available cars API
Route::get('/admin/available-cars', function () {
    $users = App\Models\User::select('id','name','email','position_id')->get();
    $brands = App\Models\CarModel::selectRaw('distinct brand')->pluck('brand');
    $drivers = App\Models\Driver::select('id','full_name')->get();
    $categories = App\Models\ComfortCategory::select('id','name','rank')->orderBy('rank')->get();

    return view('admin_available_cars', compact('users','brands','drivers','categories'));
});
