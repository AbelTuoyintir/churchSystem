<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Fund::query()->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Create form not implemented yet.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_tax_deductible' => ['boolean'],
            'goal_amount' => ['nullable', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        $fund = Fund::create($validated);

        return response()->json($fund, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund)
    {
        return response()->json($fund);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fund $fund)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'fund' => $fund]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fund $fund)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_tax_deductible' => ['sometimes', 'boolean'],
            'goal_amount' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $fund->update($validated);

        return response()->json($fund->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fund $fund)
    {
        $fund->delete();

        return response()->json(null, 204);
    }
}
