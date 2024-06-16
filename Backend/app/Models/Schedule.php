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

    public static function isTimeSlotAvailable($masterId, $date, $startTime, $endTime)
    {
        return !Appointment::where('appointment_date', $date)
            ->whereHas('masters', function ($query) use ($masterId, $startTime, $endTime) {
                $query->where('master_id', $masterId)
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('appointment_to_masters.start_time', '<', $endTime)
                            ->where('appointment_to_masters.end_time', '>', $startTime);
                    });
            })
            ->exists();
    }

    public static function getAvailableTimeSlots($date)
    {
        $schedules = self::where('date', $date)->get();
        $timeSlots = [];

        foreach ($schedules as $schedule) {
            $startTime = strtotime($schedule->start_time);
            $endTime = strtotime($schedule->end_time);

            while ($startTime < $endTime) {
                $timeSlots[] = date('H:i', $startTime);
                $startTime += 3600; // 1 час
            }
        }

        return array_unique($timeSlots);
    }
}
