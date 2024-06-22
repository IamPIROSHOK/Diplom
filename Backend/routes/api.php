<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
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

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/masters', [MasterController::class, 'store']);
    Route::put('/masters/{id}', [MasterController::class, 'update']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/chats', [ChatController::class, 'createChat']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/masters', [MasterController::class, 'index']);
Route::get('/masters/{id}', [MasterController::class, 'show']);
Route::get('/masters/{id}/services', [MasterController::class, 'getServices']);

Route::get('/schedules', [ScheduleController::class, 'index']);
Route::post('/schedules', [ScheduleController::class, 'store']);
Route::post('/schedules/available-times', [ScheduleController::class, 'getAvailableTimes']);
Route::get('schedules/{id}', [ScheduleController::class, 'show']);
Route::put('schedules/{id}', [ScheduleController::class, 'update']);
Route::delete('schedules/{id}', [ScheduleController::class, 'destroy']);
Route::post('/appointments', [AppointmentController::class, 'store']);

Route::post('/get-available-masters-and-services', [AppointmentController::class, 'getAvailableMastersAndServices']);
Route::post('/get-available-time-slots-and-services', [AppointmentController::class, 'getAvailableTimeSlotsAndServices']);
Route::post('/get-available-services', [AppointmentController::class, 'getAvailableServices']);
Route::get('/services', [AppointmentController::class, 'getAllServices']);
Route::get('/services', [ServiceController::class, 'index']);
