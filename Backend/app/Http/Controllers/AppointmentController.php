<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Master;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function getAvailableTimeSlots(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
        ]);

        $appointmentDate = $request->appointment_date;
        $schedules = Schedule::where('date', $appointmentDate)->get();
        $bookedSlots = Appointment::where('appointment_date', $appointmentDate)
            ->with('masters')
            ->get()
            ->flatMap(function ($appointment) {
                return $appointment->masters->map(function ($master) use ($appointment) {
                    return [
                        'master_id' => $master->pivot->master_id,
                        'start_time' => $master->pivot->start_time,
                        'end_time' => $master->pivot->end_time,
                    ];
                });
            });

        $timeSlots = [];

        foreach ($schedules as $schedule) {
            $startTime = strtotime($schedule->start_time);
            $endTime = strtotime($schedule->end_time);

            while ($startTime < $endTime) {
                $slotStartTime = date('H:i', $startTime);
                $slotEndTime = date('H:i', $startTime + 1800); // 30 минут

                // Проверяем, есть ли конфликты с уже забронированными слотами
                $isBooked = $bookedSlots->contains(function ($slot) use ($schedule, $slotStartTime, $slotEndTime) {
                    return $slot['master_id'] === $schedule->master_id &&
                        !($slot['end_time'] <= $slotStartTime || $slot['start_time'] >= $slotEndTime);
                });

                if (!$isBooked) {
                    $timeSlots[] = $slotStartTime;
                }

                $startTime += 1800; // 30 минут
            }
        }

        // Сортируем временные слоты по возрастанию
        sort($timeSlots);

        return response()->json(['time_slots' => array_unique($timeSlots)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.master_id' => 'required|exists:masters,id',
        ]);

        $startTime = $validated['start_time'];

        foreach ($validated['services'] as $serviceData) {
            $service = Service::find($serviceData['service_id']);
            $masterId = $serviceData['master_id'];
            $endTime = date('H:i', strtotime($startTime) + $service->duration * 60); // Рассчитываем время окончания на основе длительности услуги

            if (!Schedule::isTimeSlotAvailable($masterId, $validated['appointment_date'], $startTime, $endTime)) {
                return response()->json(['message' => 'The selected time slot is already booked'], 400);
            }
        }

        try {
            DB::beginTransaction();

            $appointment = Appointment::create([
                'user_id' => $validated['user_id'],
                'appointment_date' => $validated['appointment_date'],
                'status' => 'pending',
            ]);

            foreach ($validated['services'] as $serviceData) {
                $service = Service::find($serviceData['service_id']);
                $masterId = $serviceData['master_id'];
                $endTime = date('H:i', strtotime($startTime) + $service->duration * 60); // Рассчитываем время окончания на основе длительности услуги

                $appointment->services()->attach($serviceData['service_id']);
                $appointment->masters()->attach($masterId, [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Booking successful', 'appointment' => $appointment]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error booking appointment: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while booking the appointment'], 500);
        }
    }

    public function getAvailableMastersAndServices(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $appointmentDate = $request->appointment_date;
        $startTime = $request->start_time;
        $endTime = date('H:i', strtotime($startTime) + 1800); // 30 минут

        // Находим всех мастеров, которые свободны в указанное время
        $masters = Schedule::where('date', $appointmentDate)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->with(['master.services'])
            ->get()
            ->filter(function ($schedule) use ($appointmentDate, $startTime, $endTime) {
                return !Appointment::where('appointment_date', $appointmentDate)
                    ->whereHas('masters', function ($query) use ($schedule, $startTime, $endTime) {
                        $query->where('master_id', $schedule->master_id)
                            ->where(function ($query) use ($startTime, $endTime) {
                                $query->where('appointment_to_masters.start_time', '<', $endTime)
                                    ->where('appointment_to_masters.end_time', '>', $startTime);
                            });
                    })
                    ->exists();
            })
            ->pluck('master');

        return response()->json(['masters' => $masters]);
    }
}
