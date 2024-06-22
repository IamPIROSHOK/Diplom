<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'percentage'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'discounts_to_services');
    }
}
