<?php

namespace App\Http\Controllers;

use App\Models\HouseholdMember;
use App\Models\Household;
use Illuminate\Http\Request;

class HouseholdMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Household::class);

        return response()->json(HouseholdMember::query()->with(['household', 'person'])->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Household::class);

        return response()->json(['message' => 'Create form not implemented yet.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_id' => ['required', 'integer', 'exists:households,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'role' => ['nullable', 'string', 'max:255'],
        ]);

        $this->authorize('update', Household::query()->findOrFail($validated['household_id']));

        $member = HouseholdMember::create($validated);

        return response()->json($member, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HouseholdMember $householdMember)
    {
        $this->authorize('view', $householdMember->household);

        return response()->json($householdMember->load(['household', 'person']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HouseholdMember $householdMember)
    {
        $this->authorize('update', $householdMember->household);

        return response()->json(['message' => 'Edit form not implemented yet.', 'householdMember' => $householdMember]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseholdMember $householdMember)
    {
        $this->authorize('update', $householdMember->household);

        $validated = $request->validate([
            'household_id' => ['sometimes', 'integer', 'exists:households,id'],
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'role' => ['nullable', 'string', 'max:255'],
        ]);

        $householdMember->update($validated);

        return response()->json($householdMember->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseholdMember $householdMember)
    {
        $this->authorize('update', $householdMember->household);

        $householdMember->delete();

        return response()->json(null, 204);
    }
}
