<section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Audit log</h2>
                <p class="mt-1 text-sm text-gray-500">Sensitive reads, changes, role updates, and exports. Entries cannot be edited or deleted.</p>
            </div>
            <button type="button" wire:click="export" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:opacity-60">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                Export log
            </button>
        </div>

        <div class="grid gap-3 border-b border-gray-200 p-5 sm:grid-cols-2 xl:grid-cols-4">
            <div>
                <label for="audit-search" class="sr-only">Search audit log</label>
                <input id="audit-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search person, action, IP" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="audit-action" class="sr-only">Filter by action</label>
                <select id="audit-action" wire:model.live="action" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">All actions</option>
                    @foreach ($actions as $actionOption)
                        <option value="{{ $actionOption }}">{{ ucfirst($actionOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="audit-from" class="sr-only">From date</label>
                <input id="audit-from" type="date" wire:model.live="fromDate" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="audit-through" class="sr-only">Through date</label>
                <input id="audit-through" type="date" wire:model.live="throughDate" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                    <tr><th class="px-5 py-3">When</th><th class="px-5 py-3">Who</th><th class="px-5 py-3">Action</th><th class="px-5 py-3">What</th><th class="px-5 py-3">From IP</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($entries as $entry)
                        <tr wire:key="audit-entry-{{ $entry->id }}" class="align-top">
                            <td class="whitespace-nowrap px-5 py-3 text-gray-600">{{ $entry->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-gray-800">{{ $entry->actor?->name ?? 'Deleted user' }}</td>
                            <td class="whitespace-nowrap px-5 py-3"><span class="rounded bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ $entry->action }}</span></td>
                            <td class="min-w-64 px-5 py-3 text-gray-700">
                                <p>{{ $entry->description }}</p>
                                @if ($entry->subject_type)
                                    <p class="mt-1 text-xs text-gray-500">{{ $entry->subject_type }}{{ $entry->subject_id ? ' #'.$entry->subject_id : '' }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-gray-600">{{ $entry->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No audit records match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">{{ $entries->links() }}</div>
        @endif
    </section>
