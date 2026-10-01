<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;

class Form extends Component
{
    public ?Service $service = null;

    public string $name = '';
    public int $day_of_week = 0;
    public string $start_time = '09:00';
    public string $location = '';
    public bool $is_active = true;

    public function mount(?Service $service = null): void
    {
        if ($service && $service->exists) {
            $this->authorize('view', $service);
            $this->service = $service;

            $this->name = $service->name ?? '';
            $this->day_of_week = (int) $service->day_of_week;
            $this->start_time = $service->start_time ? \Illuminate\Support\Carbon::parse($service->start_time)->format('H:i') : '09:00';
            $this->location = $service->location ?? '';
            $this->is_active = (bool) $service->is_active;
        } else {
            $this->authorize('create', Service::class);
            $this->service = new Service();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->service && $this->service->exists) {
            $this->authorize('update', $this->service);
        } else {
            $this->authorize('create', Service::class);
        }

        $validated = $this->validate();

        if ($this->service && $this->service->exists) {
            $this->service->update($validated);
            session()->flash('success', 'Service updated successfully.');
        } else {
            $this->service = Service::create($validated);
            session()->flash('success', 'Service created successfully.');
        }

        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.form')->layout('layouts.app');
    }
}
