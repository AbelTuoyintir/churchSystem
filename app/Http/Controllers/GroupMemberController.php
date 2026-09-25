<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GroupMember::query()->with(['group', 'person'])->latest()->get());
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
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'role' => ['nullable', 'string', 'max:255'],
            'joined_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
        ]);

        $member = GroupMember::create($validated);

        return response()->json($member, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(GroupMember $groupMember)
    {
        return response()->json($groupMember->load(['group', 'person']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GroupMember $groupMember)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'groupMember' => $groupMember]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GroupMember $groupMember)
    {
        $validated = $request->validate([
            'group_id' => ['sometimes', 'integer', 'exists:groups,id'],
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'role' => ['nullable', 'string', 'max:255'],
            'joined_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
        ]);

        $groupMember->update($validated);

        return response()->json($groupMember->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupMember $groupMember)
    {
        $groupMember->delete();

        return response()->json(null, 204);
    }
}
