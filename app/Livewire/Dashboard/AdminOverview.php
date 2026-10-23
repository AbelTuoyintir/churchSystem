<?php

namespace App\Livewire\Dashboard;

use App\Models\Contribution;
use App\Models\Group;
use App\Models\Household;
use App\Models\Person;
use Livewire\Component;

class AdminOverview extends Component
{
    public function render()
    {
        $user = auth()->user();

        $totalPeople = Person::count();
        $totalHouseholds = Household::count();
        $totalGroups = Group::where('status', 'active')->count();

        $canViewFinancials = in_array($user?->role, ['admin', 'staff']);
        $totalContributionsThisMonth = 0;

        if ($canViewFinancials) {
            $totalContributionsThisMonth = Contribution::whereMonth('received_at', now()->month)
                ->whereYear('received_at', now()->year)
                ->sum('amount');
        }

        return view('livewire.dashboard.admin-overview', [
            'totalPeople' => $totalPeople,
            'totalHouseholds' => $totalHouseholds,
            'totalGroups' => $totalGroups,
            'canViewFinancials' => $canViewFinancials,
            'totalContributionsThisMonth' => $totalContributionsThisMonth,
        ]);
    }
}
