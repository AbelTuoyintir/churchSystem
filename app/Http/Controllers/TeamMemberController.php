<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(TeamMember::query()->with(['team', 'person', 'position'])->latest()->get());
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
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'position_id' => ['nullable', 'integer', 'exists:team_positions,id'],
            'joined_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
        ]);

        $teamMember = TeamMember::create($validated);

        return response()->json($teamMember, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember)
    {
        return response()->json($teamMember->load(['team', 'person', 'position']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamMember $teamMember)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'teamMember' => $teamMember]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'team_id' => ['sometimes', 'integer', 'exists:teams,id'],
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'position_id' => ['nullable', 'integer', 'exists:team_positions,id'],
            'joined_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
        ]);

        $teamMember->update($validated);

        return response()->json($teamMember->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return response()->json(null, 204);
    }
}
