<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = ['master_id', 'date', 'start_time', 'end_time'];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }


    public static function isTimeSlotAvailable($masterId, $appointmentDate, $startTime, $endTime)
    {
        // Проверка на наличие расписания
        $schedule = self::where('master_id', $masterId)
            ->where('date', $appointmentDate)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();

        if (!$schedule) {
            return false;
        }

        // Проверка на наличие конфликтующих записей
        $appointmentConflict = Appointment::where('appointment_date', $appointmentDate)
            ->whereHas('masters', function ($query) use ($masterId, $startTime, $endTime) {
                $query->where('master_id', $masterId)
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('appointment_to_masters.start_time', '<', $endTime)
                            ->where('appointment_to_masters.end_time', '>', $startTime);
                    });
            })->exists();

        return !$appointmentConflict;
    }
}
