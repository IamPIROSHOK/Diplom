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

//    public function store(Request $request)
//    {
//        $request->validate([
//            'name' => 'required|string|max:255',
//            'description' => 'required|string',
//            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//        ]);
//
//        $photoPath = null;
//        if ($request->hasFile('photo')) {
//            $photoPath = $request->file('photo')->store('uploads/masters', 'public');
//        }
//
//        $master = Master::create([
//            'name' => $request->name,
//            'description' => $request->description,
//            'photo' => $photoPath ? url($photoPath) : null,
//        ]);
//
//        return response()->json($master, 201);
//    }
}
