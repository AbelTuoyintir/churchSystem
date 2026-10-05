<?php

namespace App\Livewire\Personas;

use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function switchToPersona(string $persona)
    {
        if ($persona === 'guest') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return $this->redirect('/', navigate: true);
        }

        $user = Auth::user();

        if (! $user) {
            // If currently guest and switching to an auth persona, pick or create a user
            $user = User::first();

            if (! $user) {
                $person = Person::create([
                    'first_name' => 'Demo',
                    'last_name' => 'User',
                    'email' => 'demo@example.com',
                    'membership_status' => 'member',
                ]);

                $user = User::create([
                    'name' => 'Demo User',
                    'email' => 'demo@example.com',
                    'password' => bcrypt('password'),
                    'person_id' => $person->id,
                    'role' => 'member',
                ]);
            }

            Auth::login($user);
        }

        // Ensure user has associated Person model
        if (! $user->person_id) {
            $person = Person::create([
                'first_name' => explode(' ', $user->name)[0] ?? 'User',
                'last_name' => explode(' ', $user->name)[1] ?? 'Persona',
                'email' => $user->email,
                'membership_status' => 'member',
            ]);
            $user->person_id = $person->id;
            $user->save();
        }

        if ($persona === 'admin') {
            $user->role = 'admin';
            $user->save();

            return $this->redirect('/dashboard', navigate: true);
        }

        if ($persona === 'member') {
            $user->role = 'member';
            $user->save();

            return $this->redirect('/member-portal', navigate: true);
        }

        if ($persona === 'leader') {
            $user->role = 'leader';
            $user->save();

            // Ensure this user leads at least one group
            $group = Group::where('leader_id', $user->person_id)->first();
            if (! $group) {
                Group::create([
                    'name' => 'Leadership Small Group',
                    'description' => 'Group led by group leader persona',
                    'leader_id' => $user->person_id,
                    'type' => 'small_group',
                    'is_active' => true,
                ]);
            }

            return $this->redirect('/groups', navigate: true);
        }
    }

    public function render()
    {
        $currentUser = Auth::user();
        $ledGroupCount = 0;

        if ($currentUser && $currentUser->person_id) {
            $ledGroupCount = Group::where('leader_id', $currentUser->person_id)->count();
        }

        return view('livewire.personas.index', [
            'currentUser' => $currentUser,
            'ledGroupCount' => $ledGroupCount,
        ])->layout('layouts.app', ['header' => 'User Personas']);
    }
}
