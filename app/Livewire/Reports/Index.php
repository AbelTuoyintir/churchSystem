<?php

namespace App\Livewire\Reports;

use App\Models\Attendance;
use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Group;
use App\Models\Household;
use App\Models\PastoralCareNote;
use App\Models\Person;
use App\Models\PrayerRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()?->isStaff() || Auth::user()?->role === 'pastor', 403);
    }

    public function render()
    {
        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        $attendanceThisWeek = Attendance::query()
            ->whereBetween('attended_on', [$thisWeekStart->toDateString(), $thisWeekEnd->toDateString()])
            ->where('status', 'present')
            ->count();

        $attendanceLastWeek = Attendance::query()
            ->whereBetween('attended_on', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])
            ->where('status', 'present')
            ->count();

        $newVisitorsThisMonth = Person::query()
            ->whereMonth('joined_at', now()->month)
            ->whereYear('joined_at', now()->year)
            ->count();

        $openPrayerRequests = PrayerRequest::query()
            ->where('status', 'open')
            ->count();

        $pastoralFollowups = PastoralCareNote::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $funds = Fund::query()->withSum('contributions', 'amount')->orderBy('name')->get();
        $givingThisMonth = Contribution::query()
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->sum('amount');

        $givingLastMonth = Contribution::query()
            ->whereMonth('received_at', now()->subMonth()->month)
            ->whereYear('received_at', now()->subMonth()->year)
            ->sum('amount');

        $activeGroups = Group::query()->where('is_active', true)->count();
        $totalPeople = Person::query()->count();
        $totalHouseholds = Household::query()->count();

        return view('livewire.reports.index', [
            'attendanceThisWeek' => $attendanceThisWeek,
            'attendanceLastWeek' => $attendanceLastWeek,
            'newVisitorsThisMonth' => $newVisitorsThisMonth,
            'openPrayerRequests' => $openPrayerRequests,
            'pastoralFollowups' => $pastoralFollowups,
            'funds' => $funds,
            'givingThisMonth' => $givingThisMonth,
            'givingLastMonth' => $givingLastMonth,
            'activeGroups' => $activeGroups,
            'totalPeople' => $totalPeople,
            'totalHouseholds' => $totalHouseholds,
        ])->layout('layouts.app', ['header' => 'Reports']);
    }
}

