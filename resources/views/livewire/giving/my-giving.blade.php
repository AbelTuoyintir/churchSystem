<div class="max-w-7xl mx-auto space-y-6">
    <!-- Top Summary Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="text-xs uppercase tracking-wider font-semibold text-gray-500">Personal Giving Overview</div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">My Giving &amp; Pledges</h2>
            <p class="text-sm text-gray-500 mt-1">
                View your personal contribution records, tithes, offering receipts, and pledge progress.
            </p>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-right">
                <span class="text-xs font-semibold text-indigo-700 uppercase">Total Given ({{ $selectedYear ?: 'All Time' }})</span>
                <div class="text-2xl sm:text-3xl font-black text-indigo-950 mt-0.5">
                    {{ $currency }} {{ number_format($totalGivenThisYear, 2) }}
                </div>
            </div>

            <a href="{{ route('giving.pay') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none shrink-0">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Give Now
            </a>
        </div>
    </div>

    <!-- Active Pledges Section -->
    @if($pledges->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    My Active Pledges
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($pledges as $pledge)
                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-bold text-gray-900 text-base">{{ $pledge->fund?->name ?? 'General Fund' }}</span>
                                <div class="text-xs text-gray-500 capitalize">
                                    {{ $pledge->frequency }} pledge &bull;
                                    {{ $pledge->start_date?->format('M Y') }} - {{ $pledge->end_date?->format('M Y') }}
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $pledge->progress_percent >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ $pledge->progress_percent }}% Fulfilled
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pledge->progress_percent }}%"></div>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600">
                                Paid: <strong>{{ $currency }} {{ number_format($pledge->fulfilled_amount, 2) }}</strong>
                                of {{ $currency }} {{ number_format($pledge->amount, 2) }}
                            </span>
                            @if($pledge->remaining_amount > 0)
                                <a href="{{ route('giving.pay', ['pledge_id' => $pledge->id]) }}" class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-800">
                                    Fulfill Now &rarr;
                                </a>
                            @else
                                <span class="text-emerald-600 font-bold">Completed!</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Contributions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-bold text-gray-900">Contribution History</h3>

            <div class="flex items-center gap-2">
                <label for="year" class="text-xs font-semibold text-gray-500 uppercase">Year:</label>
                <select id="year" wire:model.live="selectedYear" class="rounded-lg border-gray-300 text-xs font-medium focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Years</option>
                    @for($y = (int) now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        @if($contributions->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-medium text-gray-700">No contributions recorded for this period.</p>
                <p class="text-xs text-gray-400 mt-1">When you give online or in-service, your records will appear here.</p>
                <a href="{{ route('giving.pay') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Make your first contribution &rarr;
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">Fund / Designation</th>
                            <th class="px-6 py-3.5">Note / Type</th>
                            <th class="px-6 py-3.5">Method</th>
                            <th class="px-6 py-3.5 text-right">Amount</th>
                            <th class="px-6 py-3.5 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($contributions as $item)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $item->contributed_on ? $item->contributed_on->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $item->fund?->name ?? 'General Fund' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $item->note ?: 'General Giving' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->method === 'paystack' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100 text-gray-700' }} capitalize">
                                        {{ $item->method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-gray-900 text-right whitespace-nowrap">
                                    {{ $currency }} {{ number_format($item->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($item->reference)
                                        <a href="{{ route('payment.receipt', $item->reference) }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Receipt
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $contributions->links() }}
            </div>
        @endif
    </div>
</div>
