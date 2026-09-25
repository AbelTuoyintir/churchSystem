<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Pledge::query()->with(['person', 'fund'])->latest()->get());
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
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'fund_id' => ['required', 'integer', 'exists:funds,id'],
            'amount' => ['required', 'numeric'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $pledge = Pledge::create($validated);

        return response()->json($pledge, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pledge $pledge)
    {
        return response()->json($pledge->load(['person', 'fund']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pledge $pledge)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'pledge' => $pledge]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pledge $pledge)
    {
        $validated = $request->validate([
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'fund_id' => ['sometimes', 'integer', 'exists:funds,id'],
            'amount' => ['sometimes', 'numeric'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $pledge->update($validated);

        return response()->json($pledge->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pledge $pledge)
    {
        $pledge->delete();

        return response()->json(null, 204);
    }
}
