<?php

namespace App\Http\Controllers;

use App\Models\TeamPosition;
use Illuminate\Http\Request;

class TeamPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(TeamPosition::query()->with('team')->latest()->get());
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
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slots_per_service' => ['nullable', 'integer'],
        ]);

        $position = TeamPosition::create($validated);

        return response()->json($position, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamPosition $teamPosition)
    {
        return response()->json($teamPosition->load(['team', 'members', 'assignments']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamPosition $teamPosition)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'teamPosition' => $teamPosition]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamPosition $teamPosition)
    {
        $validated = $request->validate([
            'team_id' => ['sometimes', 'integer', 'exists:teams,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slots_per_service' => ['nullable', 'integer'],
        ]);

        $teamPosition->update($validated);

        return response()->json($teamPosition->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamPosition $teamPosition)
    {
        $teamPosition->delete();

        return response()->json(null, 204);
    }
}
