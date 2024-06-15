<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

    public function getAvailableTimes(Request $request)
    {
        $validated = $request->validate([
            'master_id' => 'required|exists:masters,id',
            'date' => 'required|date',
        ]);

        $schedule = Schedule::where('master_id', $validated['master_id'])
            ->whereDate('date', $validated['date'])
            ->first();

        if (!$schedule) {
            return response()->json([], 200);
        }

        $startTime = strtotime($schedule->start_time);
        $endTime = strtotime($schedule->end_time);
        $availableTimes = [];

        while ($startTime < $endTime) {
            $availableTimes[] = date('H:i', $startTime);
            $startTime += 1800; // Шаг 30 минут
        }

        return response()->json($availableTimes);
    }

    public function index()
    {
        return Schedule::all();
    }

    public function store(Request $request)
    {
        $schedule = Schedule::create($request->all());
        return response()->json($schedule, 201);
    }

    public function show($id)
    {
        return Schedule::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->all());
        return response()->json($schedule, 200);
    }

    public function destroy($id)
    {
        Schedule::destroy($id);
        return response()->json(null, 204);
    }
}
