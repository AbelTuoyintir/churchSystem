<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;

class Form extends Component
{
    public ?Event $event = null;

    public string $title = '';
    public string $description = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public bool $all_day = false;
    public string $location = '';
    public string $category = '';
    public bool $is_public = true;

    public function mount(?Event $event = null): void
    {
        if ($event && $event->exists) {
            $this->authorize('view', $event);
            $this->event = $event;

            $this->title = $event->title ?? '';
            $this->description = $event->description ?? '';
            $this->starts_at = $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '';
            $this->ends_at = $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '';
            $this->all_day = (bool) $event->all_day;
            $this->location = $event->location ?? '';
            $this->category = $event->category ?? '';
            $this->is_public = (bool) $event->is_public;
        } else {
            $this->authorize('create', Event::class);
            $this->event = new Event();
            $this->starts_at = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        }
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->event && $this->event->exists) {
            $this->authorize('update', $this->event);
        } else {
            $this->authorize('create', Event::class);
        }

        $validated = $this->validate();

        if (empty($validated['ends_at'])) {
            $validated['ends_at'] = null;
        }

        if ($this->event && $this->event->exists) {
            $this->event->update($validated);
            session()->flash('success', 'Event updated successfully.');
        } else {
            $this->event = Event::create($validated);
            session()->flash('success', 'Event created successfully.');
        }

        return redirect()->route('events.index');
    }

    public function render()
    {
        return view('livewire.events.form')->layout('layouts.app', [
            'header' => $this->event && $this->event->exists ? 'Edit Event' : 'Create Event',
        ]);
    }
}
