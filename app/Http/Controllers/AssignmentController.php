<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Assignment::query()->with(['team', 'position', 'person', 'service', 'event'])->latest()->get());
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
            'position_id' => ['nullable', 'integer', 'exists:team_positions,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'assigned_on' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'reminded_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $assignment = Assignment::create($validated);

        return response()->json($assignment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        return response()->json($assignment->load(['team', 'position', 'person', 'service', 'event']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'assignment' => $assignment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'team_id' => ['sometimes', 'integer', 'exists:teams,id'],
            'position_id' => ['nullable', 'integer', 'exists:team_positions,id'],
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'assigned_on' => ['sometimes', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'reminded_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $assignment->update($validated);

        return response()->json($assignment->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return response()->json(null, 204);
    }
}
