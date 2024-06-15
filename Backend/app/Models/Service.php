<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'duration'];

    public function masters()
    {
        return $this->belongsToMany(Master::class, 'masters_to_services');
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_to_services')
            ->withPivot('start_time', 'end_time');
    }
}
