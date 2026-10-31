<?php

namespace App\Http\Controllers;

use App\Models\MessageRecipient;
use Illuminate\Http\Request;

class MessageRecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeStaff();

        return response()->json(MessageRecipient::query()->with(['message', 'person'])->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeStaff();

        return response()->json(['message' => 'Create form not implemented yet.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'message_id' => ['required', 'integer', 'exists:messages,id'],
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'channel' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'sent_at' => ['nullable', 'date'],
            'error' => ['nullable', 'string'],
        ]);

        $recipient = MessageRecipient::create($validated);

        return response()->json($recipient, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MessageRecipient $messageRecipient)
    {
        $this->authorizeStaff();

        return response()->json($messageRecipient->load(['message', 'person']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageRecipient $messageRecipient)
    {
        $this->authorizeStaff();

        return response()->json(['message' => 'Edit form not implemented yet.', 'messageRecipient' => $messageRecipient]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MessageRecipient $messageRecipient)
    {
        $this->authorizeStaff();

        $validated = $request->validate([
            'message_id' => ['sometimes', 'integer', 'exists:messages,id'],
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'channel' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'sent_at' => ['nullable', 'date'],
            'error' => ['nullable', 'string'],
        ]);

        $messageRecipient->update($validated);

        return response()->json($messageRecipient->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageRecipient $messageRecipient)
    {
        $this->authorizeStaff();

        $messageRecipient->delete();

        return response()->json(null, 204);
    }

    private function authorizeStaff(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'pastor', 'staff'], true), 403);
    }
}
