<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Master;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.master_id' => 'required|exists:masters,id',
            'services.*.start_time' => 'required|date_format:H:i',
            'appointment_date' => 'required|date',
        ]);

        foreach ($validated['services'] as $service) {
            // Проверка, что мастер предоставляет выбранную услугу
            $master = Master::find($service['master_id']);
            if (!$master->services->contains($service['service_id'])) {
                return response()->json(['message' => 'The selected master does not provide this service'], 400);
            }

            $serviceInstance = Service::find($service['service_id']);
            $startTime = $service['start_time'];
            $endTime = date('H:i', strtotime($startTime) + $serviceInstance->duration * 60);

            // Проверка на наличие доступного времени у мастера
            if (!Schedule::isTimeSlotAvailable($service['master_id'], $validated['appointment_date'], $startTime, $endTime)) {
                return response()->json(['message' => 'The selected time slot is already booked'], 400);
            }
        }

        $appointment = Appointment::create([
            'user_id' => $validated['user_id'],
            'appointment_date' => $validated['appointment_date'],
            'status' => 'pending',
        ]);

        foreach ($validated['services'] as $service) {
            $serviceInstance = Service::find($service['service_id']);
            $startTime = $service['start_time'];
            $endTime = date('H:i', strtotime($startTime) + $serviceInstance->duration * 60);

            $appointment->services()->attach($service['service_id']);
            $appointment->masters()->attach($service['master_id'], [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);
        }

        return response()->json(['message' => 'Booking successful', 'appointment' => $appointment]);
    }
}
