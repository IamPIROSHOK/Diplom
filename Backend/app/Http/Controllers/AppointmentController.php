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


    public function getAvailableTimeSlotsAndServices(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'nullable|date',
            'master_id' => 'nullable|exists:masters,id',
            'start_time' => 'nullable|date_format:H:i',
        ]);

        Log::info('Validated data:', $validated);

        $appointmentDate = $validated['appointment_date'];
        $masterId = $validated['master_id'];
        $startTime = $validated['start_time'];

        // Получение доступных временных слотов
        $timeSlots = $this->fetchTimeSlots($appointmentDate, $masterId, $startTime);

        // Получение доступных услуг
        $services = $this->fetchServices($appointmentDate, $masterId, $startTime);

        return response()->json([
            'time_slots' => $timeSlots,
            'services' => $services,
        ]);
    }

    // Метод для получения доступных временных слотов
    private function fetchTimeSlots($appointmentDate, $masterId, $startTime)
    {
        $query = Schedule::query();

        if ($appointmentDate) {
            $query->where('date', $appointmentDate);
        }

        if ($masterId) {
            $query->where('master_id', $masterId);
        }

        // Получение всех доступных временных слотов
        $schedules = $query->get();

        // Преобразование временных слотов в необходимый формат
        $timeSlots = [];
        foreach ($schedules as $schedule) {
            $current = strtotime($schedule->start_time);
            $end = strtotime($schedule->end_time);
            while ($current < $end) {
                $timeSlots[] = date('H:i', $current);
                $current = strtotime('+30 minutes', $current);
            }
        }

        return $timeSlots;
    }

    // Метод для получения доступных услуг
    private function fetchServices($appointmentDate, $masterId, $startTime)
    {
        $query = Service::query();

        if ($masterId) {
            $query->whereHas('masters', function ($query) use ($masterId) {
                $query->where('master_id', $masterId);
            });
        }

        if ($appointmentDate) {
            $query->whereHas('masters.schedules', function ($query) use ($appointmentDate) {
                $query->where('date', $appointmentDate);
            });
        }

        return $query->get();
    }

    public function getAvailableServices(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'master_id' => 'nullable|exists:masters,id',
        ]);

        $appointmentDate = $validated['appointment_date'] ?? null;
        $startTime = $validated['start_time'] ?? null;
        $endTime = $startTime ? date('H:i', strtotime($startTime) + 1800) : null; // 30 минут
        $masterId = $validated['master_id'] ?? null;

        $query = Schedule::query();

        if ($appointmentDate) {
            $query->where('date', $appointmentDate);
        }

        if ($masterId) {
            $query->where('master_id', $masterId);
        }

        if ($startTime) {
            $query->where('start_time', '<=', $startTime)->where('end_time', '>=', $endTime);
        }

        $schedules = $query->with('master.services')->get();

        $services = $schedules->flatMap(function ($schedule) {
            return $schedule->master->services;
        })->unique('id');

        return response()->json(['services' => $services]);
    }

    public function getAllServices()
    {
        $services = Service::all();
        return response()->json($services);
    }

}
