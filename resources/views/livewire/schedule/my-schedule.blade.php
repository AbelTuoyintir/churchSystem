<div class="mx-auto max-w-7xl space-y-6">
    @if (session()->has('success'))
        <div class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <header class="border-b border-gray-200 pb-5">
        <p class="text-sm font-semibold uppercase text-emerald-700">My Portal</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">My Schedule</h1>
        <p class="mt-1 text-sm text-gray-600">Your upcoming serving assignments.</p>
    </header>

    @if ($assignments->isEmpty())
        <p class="py-10 text-sm text-gray-600">No upcoming serving assignments.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Team</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Service / Event</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($assignments as $assignment)
                        <tr wire:key="assignment-{{ $assignment->id }}">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $assignment->assigned_on?->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $assignment->team?->name }}</td>
                            <td class="px-4 py-2">{{ $assignment->position?->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                {{ $assignment->service?->name ?? $assignment->event?->title ?? '—' }}
                            </td>
                            <td class="px-4 py-2 capitalize">{{ $assignment->status }}</td>
                            <td class="px-4 py-2 space-x-2">
                                @if ($assignment->status !== 'confirmed')
                                    <button type="button" wire:click="confirm({{ $assignment->id }})" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Confirm</button>
                                @endif
                                @if ($assignment->status !== 'declined')
                                    <button type="button" wire:click="decline({{ $assignment->id }})" class="text-sm font-semibold text-red-700 hover:text-red-900">Decline</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
