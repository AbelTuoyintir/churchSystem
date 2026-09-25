<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Attendance::query()->with(['person', 'service', 'event', 'group', 'recordedBy'])->latest()->get());
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
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'attended_on' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'checked_in_at' => ['nullable', 'date'],
            'headcount' => ['nullable', 'integer'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance = Attendance::create($validated);

        return response()->json($attendance, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load(['person', 'service', 'event', 'group', 'recordedBy']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'attendance' => $attendance]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'attended_on' => ['sometimes', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'checked_in_at' => ['nullable', 'date'],
            'headcount' => ['nullable', 'integer'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance->update($validated);

        return response()->json($attendance->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json(null, 204);
    }
}
