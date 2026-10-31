<?php

namespace App\Livewire\Admin\UserRoles;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public array $selectedRoles = [];

    public array $roleOptions = [
        'admin' => 'Admin',
        'pastor' => 'Pastor',
        'steward' => 'Steward',
        'member' => 'Member',
        'leader' => 'Leader',
        'staff' => 'Staff',
        'viewer' => 'Viewer',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateRole(int $userId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
        $errorKey = "selectedRoles.$userId";
        $this->resetErrorBag($errorKey);

        if ($userId === Auth::id()) {
            $message = 'You cannot change your own role.';
            $this->addError($errorKey, $message);
            $this->dispatch('notify', type: 'error', message: $message);

            return;
        }

        $role = $this->selectedRoles[$userId] ?? null;

        if (! array_key_exists($role, $this->roleOptions)) {
            $message = 'Unsupported role.';
            $this->addError($errorKey, $message);
            $this->dispatch('notify', type: 'error', message: $message);

            return;
        }

        $user = User::findOrFail($userId);
        $user->syncRoles([$role]);
        $user->role = $role;
        $user->save();

        $this->dispatch('notify', type: 'success', message: 'User role updated successfully.');
    }

    public function render()
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $users = User::query()
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(15);

        foreach ($users as $user) {
            if (! array_key_exists($user->id, $this->selectedRoles)) {
                $this->selectedRoles[$user->id] = $user->role ?? $user->roles->first()?->name ?? '';
            }
        }

        return view('livewire.admin.user-roles.index', [
            'users' => $users,
            'roleOptions' => $this->roleOptions,
            'selectedRoles' => $this->selectedRoles,
        ])->layout('layouts.app', ['header' => 'User Roles']);
    }
}
