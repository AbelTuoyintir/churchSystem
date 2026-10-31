<div class="mx-auto max-w-7xl space-y-6 print:space-y-4">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-5 print:border-0">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Church Reports</p>
            <h1 class="mt-1 text-2xl font-bold text-stone-900">Ministry Overview</h1>
            <p class="mt-1 text-sm text-stone-600">Church-wide attendance, growth, engagement, and giving trends.</p>
        </div>
        <button onclick="window.print()" class="rounded-lg bg-stone-800 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-900 print:hidden">
            Print report
        </button>
    </header>
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Attendance this week</p>
            <p class="mt-2 text-3xl font-extrabold text-stone-900">{{ number_format($attendanceThisWeek) }}</p>
            <p class="mt-1 text-xs text-stone-500">Last week: {{ number_format($attendanceLastWeek) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">New visitors this month</p>
            <p class="mt-2 text-3xl font-extrabold text-stone-900">{{ number_format($newVisitorsThisMonth) }}</p>
            <p class="mt-1 text-xs text-stone-500">People joined this month</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Open prayer requests</p>
            <p class="mt-2 text-3xl font-extrabold text-stone-900">{{ number_format($openPrayerRequests) }}</p>
            <p class="mt-1 text-xs text-stone-500">Awaiting prayer</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Pastoral follow-ups</p>
            <p class="mt-2 text-3xl font-extrabold text-stone-900">{{ number_format($pastoralFollowups) }}</p>
            <p class="mt-1 text-xs text-stone-500">Open or in progress</p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-900">Giving trends</h2>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-stone-600">This month</span>
                    <span class="text-lg font-bold text-emerald-700">${{ number_format($givingThisMonth, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-stone-600">Last month</span>
                    <span class="text-lg font-bold text-stone-700">${{ number_format($givingLastMonth, 2) }}</span>
                </div>
            </div>
            <div class="mt-6 space-y-3">
                <h3 class="text-sm font-semibold text-stone-700">Fund progress</h3>
                @forelse ($funds as $fund)
                    @php
                        $totalReceived = (float) ($fund->contributions_sum_amount ?? 0);
                        $progress = $fund->goal_amount > 0 ? min(100, round(($totalReceived / $fund->goal_amount) * 100)) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-stone-700">{{ $fund->name }}</span>
                            <span class="text-stone-500">${{ number_format($totalReceived, 2) }} / ${{ number_format($fund->goal_amount, 2) }}</span>
                        </div>
                        <div class="mt-1 h-2 w-full rounded-full bg-stone-100">
                            <div class="h-2 rounded-full bg-emerald-600" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-stone-500">No funds configured.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-900">Church snapshot</h2>
            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                <div class="rounded-lg bg-stone-50 p-4">
                    <dt class="text-stone-500">Active groups</dt>
                    <dd class="mt-1 text-2xl font-bold text-stone-900">{{ number_format($activeGroups) }}</dd>
                </div>
                <div class="rounded-lg bg-stone-50 p-4">
                    <dt class="text-stone-500">Total people</dt>
                    <dd class="mt-1 text-2xl font-bold text-stone-900">{{ number_format($totalPeople) }}</dd>
                </div>
                <div class="rounded-lg bg-stone-50 p-4">
                    <dt class="text-stone-500">Households</dt>
                    <dd class="mt-1 text-2xl font-bold text-stone-900">{{ number_format($totalHouseholds) }}</dd>
                </div>
                <div class="rounded-lg bg-stone-50 p-4">
                    <dt class="text-stone-500">Attendance trend</dt>
                    <dd class="mt-1 text-2xl font-bold text-stone-900">
                        {{ $attendanceLastWeek > 0 ? round((($attendanceThisWeek - $attendanceLastWeek) / $attendanceLastWeek) * 100) : 0 }}%
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</div>
</div>
