<?php

namespace App\Livewire\Dashboard;

use App\Models\Attendance;
use App\Models\Service;
use Livewire\Component;

class AttendanceThisWeek extends Component
{
    public function render()
    {
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $services = Service::where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $serviceStats = $services->map(function (Service $service) use ($startOfWeek, $endOfWeek) {
            $records = Attendance::where('service_id', $service->id)
                ->whereBetween('attended_on', [$startOfWeek, $endOfWeek])
                ->get();

            $totalHeadcount = $records->sum(function ($attendance) {
                return $attendance->headcount ?? 1;
            });

            return [
                'id' => $service->id,
                'name' => $service->name,
                'day_of_week' => $service->day_of_week,
                'start_time' => $service->start_time,
                'location' => $service->location,
                'total_headcount' => $totalHeadcount,
                'records_count' => $records->count(),
            ];
        });

        $grandTotal = $serviceStats->sum('total_headcount');

        return view('livewire.dashboard.attendance-this-week', [
            'serviceStats' => $serviceStats,
            'grandTotal' => $grandTotal,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
        ]);
    }
}
