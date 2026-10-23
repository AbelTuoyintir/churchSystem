<div>
    @php
        $methodStyles = [
            'cash'     => 'bg-green-100 text-green-800',
            'check'    => 'bg-yellow-100 text-yellow-800',
            'card'     => 'bg-indigo-100 text-indigo-800',
            'transfer' => 'bg-blue-100 text-blue-800',
            'online'   => 'bg-gray-100 text-gray-800',
        ];
    @endphp

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Contributions</h2>
            <p class="text-sm text-gray-500 mt-1">Every gift, by fund and method.</p>
        </div>
        @can('create', App\Models\Contribution::class)
            <a href="{{ route('contributions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium text-sm rounded-md hover:bg-indigo-700 shadow-sm">
                + Record Contribution
            </a>
        @endcan
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">

        <!-- Toolbar: search on top, filters beneath -->
        <div class="p-4 border-b border-gray-200 space-y-4">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                </svg>
                <label for="search" class="sr-only">Search</label>
                <input id="search" type="text" wire:model.live.debounce.300ms="search" placeholder="Search person, ref..." class="w-full pl-9 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 bg-gray-50 p-3 rounded-md border border-gray-200">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Fund</label>
                    <select wire:model.live="fund_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Funds</option>
                        @foreach ($funds as $f)
                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Method</label>
                    <select wire:model.live="method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="card">Card</option>
                        <option value="transfer">Transfer</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                    <input type="date" wire:model.live="date_from" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                    <input type="date" wire:model.live="date_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
        </div>

        <!-- List: contributor-first rows, amount highlighted on the right -->
        <ul class="divide-y divide-gray-200">
            @forelse ($contributions as $contribution)
                @php
                    $isAnon = $contribution->is_anonymous || !$contribution->person;
                    $name = $isAnon
                        ? 'Anonymous'
                        : ($contribution->person->full_name ?? ($contribution->person->first_name . ' ' . $contribution->person->last_name));
                @endphp
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <!-- Avatar -->
                        <div class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center text-sm font-semibold {{ $isAnon ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ $isAnon ? '?' : strtoupper(mb_substr($name, 0, 1)) }}
                        </div>

                        <!-- Contributor + meta -->
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium truncate {{ $isAnon ? 'text-gray-500 italic' : 'text-gray-900' }}">
                                {{ $name }}
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                <span>{{ $contribution->contributed_on ? $contribution->contributed_on->format('M j, Y') : '-' }}</span>
                                <span class="hidden sm:inline text-gray-300">•</span>
                                <span>{{ $contribution->fund->name ?? '-' }}</span>
                                <span class="px-2 py-0.5 rounded-full font-semibold capitalize {{ $methodStyles[$contribution->method] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $contribution->method }}
                                </span>
                            </div>
                        </div>

                        <!-- Amount + action -->
                        <div class="text-right shrink-0">
                            <div class="text-base font-semibold text-gray-900">
                                ${{ number_format((float) $contribution->amount, 2) }}
                            </div>
                            @can('update', $contribution)
                                <a href="{{ route('contributions.edit', $contribution) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endcan
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center text-gray-500 text-sm">
                    No contributions found.
                </li>
            @endforelse
        </ul>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $contributions->links() }}
        </div>
    </div>
</div>