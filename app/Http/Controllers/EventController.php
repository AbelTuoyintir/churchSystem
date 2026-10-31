<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Event::class);

        $user = auth()->user();
        $eventsQuery = Event::query();
        if ($user->person_id === null) {
            $eventsQuery->where('is_public', true);
        } else {
            $visibleGroupIds = Group::query()
                ->where(function ($query) use ($user): void {
                    $query->where('leader_id', $user->person_id)
                        ->orWhereHas('members', fn ($members) => $members
                            ->where('person_id', $user->person_id)
                            ->whereNull('left_at'));
                })
                ->select('id');

            $eventsQuery->where(function ($query) use ($visibleGroupIds): void {
                $query->where('is_public', true)->orWhereIn('group_id', $visibleGroupIds);
            });
        }

        $events = $eventsQuery->latest('starts_at')->get();

        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        return response()->json(['message' => 'Create form not implemented yet.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date'],
            'all_day' => ['boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'is_public' => ['boolean'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
        ]);

        if ($request->user()->isGroupLeader() && ! $request->user()->isStaff()) {
            abort_unless(! empty($validated['group_id']) && Group::query()
                ->ledBy($request->user())
                ->whereKey($validated['group_id'])
                ->exists(), 403);
            $validated['is_public'] = false;
        }

        $event = Event::create($validated);

        try {
            $pushService = app(PushNotificationService::class);
            $title = 'New Event: '.$event->title;
            $body = $event->description ?: 'Join us for this upcoming event.';

            if ($event->group_id !== null) {
                $pushService->sendToGroup($event->group, $title, $body, route('members.events'));
            } else {
                $pushService->sendToAll('New Church Event: '.$event->title, $body, route('members.events'));
            }
        } catch (\Throwable $e) {
            Log::info('Event push notification note: '.$e->getMessage());
        }

        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        return response()->json($event);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        return response()->json(['message' => 'Edit form not implemented yet.', 'event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['nullable', 'date'],
            'all_day' => ['sometimes', 'boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        if (! $request->user()->isStaff()) {
            unset($validated['is_public']);
        }

        $event->update($validated);

        return response()->json($event->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json(null, 204);
    }
}
