<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'appointment_date', 'status'];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_to_services');
    }

    public function masters()
    {
        return $this->belongsToMany(Master::class, 'appointment_to_masters')
            ->withPivot('start_time', 'end_time');
    }

}
