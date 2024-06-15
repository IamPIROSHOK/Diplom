<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'photo'];


    public function services()
    {
        return $this->belongsToMany(Service::class, 'masters_to_services');
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_to_masters');
    }
}
