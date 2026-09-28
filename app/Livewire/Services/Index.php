<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $isActive = '';
    public bool $confirmingDeletion = false;
    public ?int $serviceIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->serviceIdBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteService(): void
    {
        $service = Service::findOrFail($this->serviceIdBeingDeleted);
        $this->authorize('delete', $service);

        $service->delete();

        $this->confirmingDeletion = false;
        $this->serviceIdBeingDeleted = null;

        session()->flash('success', 'Service deleted successfully.');
    }

    public static function getDayName(int $dayOfWeek): string
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return $days[$dayOfWeek] ?? 'Unknown';
    }

    public function render()
    {
        $this->authorize('viewAny', Service::class);

        $services = Service::query()
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('is_active', filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('day_of_week', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('livewire.services.index', [
            'services' => $services,
        ])->layout('layouts.app');
    }
}
