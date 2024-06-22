<?php

namespace App\Http\Controllers;
use App\Models\Discount;

use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with('services')->get();
        return response()->json($discounts);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $discount = Discount::create($request->all());

        return response()->json(['message' => 'Discount created successfully', 'discount' => $discount], 201);
    }

    public function addDiscountToService(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $discount = Discount::findOrFail($request->discount_id);
        $discount->services()->attach($request->service_id);

        return response()->json(['message' => 'Discount added to service successfully']);
    }

}
