<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use App\Models\Person;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Table filters
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $serviceId = '';
    public string $groupId = '';
    public string $statusFilter = '';

    // Bulk action selected row IDs
    public array $selectedAttendanceIds = [];
    public bool $selectAll = false;

    // Modal form properties
    public bool $showingFormModal = false;
    public ?int $editingAttendanceId = null;

    public ?int $person_id = null;
    public ?int $service_id = null;
    public ?int $event_id = null;
    public ?int $group_id = null;
    public string $attended_on = '';
    public string $status = 'present';
    public ?int $headcount = null;
    public string $notes = '';

    // Search input for person selector in modal form
    public string $personSearch = '';

    // Confirmation modal for deletion
    public bool $confirmingDeletion = false;
    public ?int $attendanceIdBeingDeleted = null;

    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'serviceId' => ['except' => ''],
        'groupId' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->attended_on = now()->toDateString();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingServiceId(): void
    {
        $this->resetPage();
    }

    public function updatingGroupId(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedAttendanceIds = $this->getFilteredAttendancesQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedAttendanceIds = [];
        }
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Attendance::class);
        $this->resetForm();
        $this->showingFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $attendance = Attendance::findOrFail($id);
        $this->authorize('update', $attendance);

        $this->editingAttendanceId = $attendance->id;
        $this->person_id = $attendance->person_id;
        $this->service_id = $attendance->service_id;
        $this->event_id = $attendance->event_id;
        $this->group_id = $attendance->group_id;
        $this->attended_on = $attendance->attended_on ? $attendance->attended_on->format('Y-m-d') : now()->toDateString();
        $this->status = $attendance->status ?? 'present';
        $this->headcount = $attendance->headcount;
        $this->notes = $attendance->notes ?? '';
        $this->personSearch = '';

        $this->showingFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showingFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingAttendanceId = null;
        $this->person_id = null;
        $this->service_id = null;
        $this->event_id = null;
        $this->group_id = null;
        $this->attended_on = now()->toDateString();
        $this->status = 'present';
        $this->headcount = null;
        $this->notes = '';
        $this->personSearch = '';
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'person_id' => 'nullable|exists:people,id',
            'service_id' => 'nullable|exists:services,id',
            'event_id' => 'nullable|exists:events,id',
            'group_id' => 'nullable|exists:groups,id',
            'attended_on' => 'required|date',
            'status' => 'required|string|in:present,absent,excused,late',
            'headcount' => $this->person_id ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'headcount.required' => 'The headcount field is required when recording an anonymous attendance (person is empty).',
        ];
    }

    public function saveAttendance(): void
    {
        if ($this->editingAttendanceId) {
            $attendance = Attendance::findOrFail($this->editingAttendanceId);
            $this->authorize('update', $attendance);
        } else {
            $this->authorize('create', Attendance::class);
        }

        $validated = $this->validate();

        $data = [
            'person_id' => $validated['person_id'] ?: null,
            'service_id' => $validated['service_id'] ?: null,
            'event_id' => $validated['event_id'] ?: null,
            'group_id' => $validated['group_id'] ?: null,
            'attended_on' => $validated['attended_on'],
            'status' => $validated['status'],
            'headcount' => $validated['person_id'] ? ($validated['headcount'] ?? 1) : $validated['headcount'],
            'notes' => $validated['notes'] ?: null,
            'recorded_by' => auth()->id(),
        ];

        if ($this->editingAttendanceId) {
            $attendance->update($data);
            session()->flash('success', 'Attendance record updated successfully.');
        } else {
            Attendance::create($data);
            session()->flash('success', 'Attendance record created successfully.');
        }

        $this->closeFormModal();
    }

    public function bulkMarkStatus(string $status): void
    {
        $this->authorize('create', Attendance::class);

        if (empty($this->selectedAttendanceIds)) {
            return;
        }

        if (!in_array($status, ['present', 'absent'])) {
            return;
        }

        Attendance::whereIn('id', $this->selectedAttendanceIds)
            ->update(['status' => $status]);

        $count = count($this->selectedAttendanceIds);
        $this->selectedAttendanceIds = [];
        $this->selectAll = false;

        session()->flash('success', "Updated {$count} attendance record(s) to {$status}.");
    }

    public function confirmDelete(int $id): void
    {
        $this->attendanceIdBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteAttendance(): void
    {
        $attendance = Attendance::findOrFail($this->attendanceIdBeingDeleted);
        $this->authorize('delete', $attendance);

        $attendance->delete();

        $this->confirmingDeletion = false;
        $this->attendanceIdBeingDeleted = null;

        session()->flash('success', 'Attendance record deleted.');
    }

    public function resolveContext(Attendance $attendance): string
    {
        if ($attendance->service) {
            return 'Service: ' . $attendance->service->name;
        }

        if ($attendance->event) {
            return 'Event: ' . $attendance->event->title;
        }

        if ($attendance->group) {
            return 'Group: ' . $attendance->group->name;
        }

        return '—';
    }

    private function getFilteredAttendancesQuery()
    {
        return Attendance::query()
            ->when($this->dateFrom !== '', fn($q) => $q->where('attended_on', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn($q) => $q->where('attended_on', '<=', $this->dateTo))
            ->when($this->serviceId !== '', fn($q) => $q->where('service_id', $this->serviceId))
            ->when($this->groupId !== '', fn($q) => $q->where('group_id', $this->groupId))
            ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter));
    }

    public function render()
    {
        $this->authorize('viewAny', Attendance::class);

        $attendances = $this->getFilteredAttendancesQuery()
            ->with(['person', 'service', 'event', 'group'])
            ->orderBy('attended_on', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $peopleQuery = Person::query()->orderBy('last_name')->orderBy('first_name');
        if ($this->personSearch !== '') {
            $peopleQuery->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->personSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $this->personSearch . '%');
            });
        }
        $people = $peopleQuery->limit(50)->get();

        $services = Service::where('is_active', true)->orderBy('name')->get();
        $allServices = Service::orderBy('name')->get();
        $groups = Group::where('is_active', true)->orderBy('name')->get();
        $allGroups = Group::orderBy('name')->get();
        $events = Event::orderBy('starts_at', 'desc')->limit(50)->get();

        return view('livewire.attendance.index', [
            'attendances' => $attendances,
            'people' => $people,
            'services' => $services,
            'allServices' => $allServices,
            'groups' => $groups,
            'allGroups' => $allGroups,
            'events' => $events,
        ])->layout('layouts.app');
    }
}
