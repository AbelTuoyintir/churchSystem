<?php

namespace App\Http\Controllers;

use App\Models\TeamPosition;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Team::class);

        return response()->json(TeamPosition::query()->with('team')->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Team::class);

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

        $this->authorize('create', Team::class);

        $position = TeamPosition::create($validated);

        return response()->json($position, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamPosition $teamPosition)
    {
        $this->authorize('view', $teamPosition->team);

        return response()->json($teamPosition->load(['team', 'members', 'assignments']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamPosition $teamPosition)
    {
        $this->authorize('update', $teamPosition->team);

        return response()->json(['message' => 'Edit form not implemented yet.', 'teamPosition' => $teamPosition]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamPosition $teamPosition)
    {
        $this->authorize('update', $teamPosition->team);

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
        $this->authorize('update', $teamPosition->team);

        $teamPosition->delete();

        return response()->json(null, 204);
    }
}
