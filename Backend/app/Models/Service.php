<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'duration', 'photo'];

    protected $casts = [
        'parallel_services' => 'array',
    ];

    public function masters()
    {
        return $this->belongsToMany(Master::class, 'masters_to_services');
    }

}
