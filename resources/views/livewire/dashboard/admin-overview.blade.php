<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total People Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total People</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalPeople) }}</p>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
            </svg>
        </div>
    </div>

    <!-- Active Groups Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Groups</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalGroups) }}</p>
        </div>
        <div class="p-3 bg-green-50 text-green-600 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
    </div>

    <!-- Households Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Households</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalHouseholds) }}</p>
        </div>
        <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
    </div>

    <!-- Financials Card (Admin/Staff only) -->
    @if($canViewFinancials)
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Giving This Month</p>
                <p class="text-3xl font-extrabold text-emerald-600 mt-1">${{ number_format($totalContributionsThisMonth, 2) }}</p>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Role Access</p>
                <p class="text-base font-semibold text-gray-700 mt-1 capitalize">{{ auth()->user()->role }} Overview</p>
            </div>
            <div class="p-3 bg-purple-50 text-purple-600 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    @endif
</div>
