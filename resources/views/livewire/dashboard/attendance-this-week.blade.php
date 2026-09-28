<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Attendance This Week</h3>
            <p class="text-xs text-gray-500">
                {{ \Illuminate\Support\Carbon::parse($startOfWeek)->format('M j, Y') }} - {{ \Illuminate\Support\Carbon::parse($endOfWeek)->format('M j, Y') }}
            </p>
        </div>
        <div class="bg-indigo-50 text-indigo-700 font-bold px-3 py-1 rounded-full text-sm">
            Total: {{ $grandTotal }}
        </div>
    </div>

    @if ($serviceStats->isEmpty())
        <p class="text-sm text-gray-500 text-center py-4">No active services found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($serviceStats as $stat)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-base">{{ $stat['name'] }}</h4>
                        <p class="text-xs text-gray-500">
                            {{ \App\Livewire\Services\Index::getDayName($stat['day_of_week']) }}
                            @if ($stat['start_time'])
                                at {{ \Illuminate\Support\Carbon::parse($stat['start_time'])->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                    <div class="mt-3 flex justify-between items-baseline pt-2 border-t border-gray-200">
                        <span class="text-xs text-gray-500">Headcount:</span>
                        <span class="text-2xl font-bold text-indigo-600">{{ $stat['total_headcount'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
