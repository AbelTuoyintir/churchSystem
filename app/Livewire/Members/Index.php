<?php

namespace App\Livewire\Members;

use App\Models\Person;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $user = auth()->user();
        $person = null;

        if ($user && $user->person_id) {
            $person = Person::query()
                ->with([
                    'households.people',
                    'groups',
                    'assignments.team',
                    'assignments.position',
                    'assignments.service',
                    'attendances.service',
                ])
                ->find($user->person_id);
        }

        return view('livewire.members.index', [
            'user' => $user,
            'person' => $person,
        ])->layout('layouts.app', ['header' => 'My Profile & Portal']);
    }
}
