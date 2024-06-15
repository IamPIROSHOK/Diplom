<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('products', ProductController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('masters', [MasterController::class, 'index']);
Route::get('masters/{id}', [MasterController::class, 'show']);

Route::get('schedules', [ScheduleController::class, 'index']);
Route::post('schedules', [ScheduleController::class, 'store']);
Route::get('schedules/{id}', [ScheduleController::class, 'show']);
Route::put('schedules/{id}', [ScheduleController::class, 'update']);
Route::delete('schedules/{id}', [ScheduleController::class, 'destroy']);

Route::post('/appointments', [AppointmentController::class, 'store']);

Route::get('/masters/{id}/services', [MasterController::class, 'getServices']);
Route::post('/schedules/available-times', [ScheduleController::class, 'getAvailableTimes']);

Route::post('/get-available-time-slots', [AppointmentController::class, 'getAvailableTimeSlots']);
Route::post('/get-available-masters-and-services', [AppointmentController::class, 'getAvailableMastersAndServices']);
Route::post('/appointments', [AppointmentController::class, 'store']);
