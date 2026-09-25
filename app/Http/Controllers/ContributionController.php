<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Contribution::query()->with(['person', 'fund', 'recordedBy'])->latest()->get());
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
            'fund_id' => ['required', 'integer', 'exists:funds,id'],
            'amount' => ['required', 'numeric'],
            'contributed_on' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'is_anonymous' => ['boolean'],
            'note' => ['nullable', 'string'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $contribution = Contribution::create($validated);

        return response()->json($contribution, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribution $contribution)
    {
        return response()->json($contribution->load(['person', 'fund', 'recordedBy']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contribution $contribution)
    {
        return response()->json(['message' => 'Edit form not implemented yet.', 'contribution' => $contribution]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contribution $contribution)
    {
        $validated = $request->validate([
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'fund_id' => ['sometimes', 'integer', 'exists:funds,id'],
            'amount' => ['sometimes', 'numeric'],
            'contributed_on' => ['sometimes', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'note' => ['nullable', 'string'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $contribution->update($validated);

        return response()->json($contribution->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return response()->json(null, 204);
    }
}
