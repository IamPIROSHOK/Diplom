<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        $defaultPhotoUrl = url('/uploads/default_photo.jpg');


        $masters = Master::all()->map(function($master) use ($defaultPhotoUrl) {
            $master->photo = $master->photo ? url($master->photo) : $defaultPhotoUrl;
            $master->services = $master->services->pluck('id'); // Добавить идентификаторы услуг мастера

//            $master->photo = $defaultPhotoUrl;

            return $master;
        });

        return response()->json($masters);
    }

    public function show($id)
    {
        return Master::with('schedules')->findOrFail($id);
    }

    public function getServices($id)
    {
        $master = Master::with('services')->findOrFail($id);
        return response()->json($master->services);
    }

}
