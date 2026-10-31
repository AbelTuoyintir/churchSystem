<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Team::class);

        return response()->json(Team::query()->with('leader')->latest()->get());
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
        $this->authorize('create', Team::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_id' => ['nullable', 'integer', 'exists:people,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'is_active' => ['boolean'],
        ]);

        $team = Team::create($validated);

        return response()->json($team, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team->load(['leader', 'positions', 'members']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $this->authorize('update', $team);

        return response()->json(['message' => 'Edit form not implemented yet.', 'team' => $team]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_id' => ['nullable', 'integer', 'exists:people,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $team->update($validated);

        return response()->json($team->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json(null, 204);
    }
}
