<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-indigo-800 to-indigo-900 rounded-2xl shadow-sm text-white p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest text-indigo-300 font-bold">Online Giving Portal</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Offerings, Tithes &amp; Donations</h2>
                <p class="text-indigo-200 text-sm mt-1">
                    "Give, and it will be given to you." — Luke 6:38. Secured by Paystack.
                </p>
            </div>
            <a href="{{ route('giving.my') }}" class="inline-flex items-center text-xs font-semibold px-3.5 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white border border-white/20 transition shrink-0">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                My Giving History
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-md text-sm text-red-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <form action="{{ route('payment.initiate') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Giving Type Selection -->
            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">Select Purpose / Category <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    @php
                        $types = [
                            'offering' => ['label' => 'Offering', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'tithe' => ['label' => 'Tithe', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            'donation' => ['label' => 'Donation', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                            'pledge' => ['label' => 'Pledge', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ];
                    @endphp

                    @foreach($types as $key => $meta)
                        <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer text-center transition {{ $payment_type === $key ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500 text-indigo-700' : 'border-gray-200 hover:bg-gray-50 text-gray-700' }}">
                            <input type="radio" wire:model.live="payment_type" name="payment_type" value="{{ $key }}" class="sr-only">
                            <svg class="w-6 h-6 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/></svg>
                            <span class="text-xs font-bold">{{ $meta['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pledge Selection (when payment_type is 'pledge') -->
            @if($payment_type === 'pledge')
                <div class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-200 space-y-2">
                    <label for="pledge_id" class="block text-sm font-bold text-indigo-950">Select Pledge to Fulfill</label>
                    @if($userPledges->isEmpty())
                        <p class="text-xs text-indigo-700">You do not have any active pledges recorded yet. You can still make a general pledge contribution below.</p>
                    @else
                        <select id="pledge_id" wire:model.live="pledge_id" name="pledge_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Choose from your recorded pledges --</option>
                            @foreach($userPledges as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->fund?->name ?? 'General' }} - Pledged {{ $currency }} {{ number_format($p->amount, 2) }} (Remaining: {{ $currency }} {{ number_format($p->remaining_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif

            <!-- Amount Section -->
            <div>
                <label for="amount" class="block text-sm font-bold text-gray-800 mb-2">Amount ({{ $currency }}) <span class="text-red-500">*</span></label>
                
                <!-- Quick Amount Presets -->
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mb-3">
                    @foreach([20, 50, 100, 200, 500] as $preset)
                        <button type="button" wire:click="setQuickAmount({{ $preset }})" class="py-2 px-3 border rounded-lg text-sm font-semibold transition {{ $amount == $preset ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white hover:bg-gray-50 text-gray-800' }}">
                            {{ $currency }} {{ $preset }}
                        </button>
                    @endforeach
                </div>

                <div class="relative rounded-xl shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <span class="text-gray-500 font-bold sm:text-lg">{{ $currency }}</span>
                    </div>
                    <input type="number" step="0.01" min="1" id="amount" wire:model="amount" name="amount" value="{{ $amount }}" required class="block w-full rounded-xl border-gray-300 pl-16 pr-4 py-3 text-lg sm:text-xl font-bold text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Fund / Designation -->
            <div>
                <label for="fund_id" class="block text-sm font-bold text-gray-800 mb-1">Fund Designation</label>
                <select id="fund_id" wire:model="fund_id" name="fund_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach($funds as $fund)
                        <option value="{{ $fund->id }}">{{ $fund->name }} @if($fund->description) - {{ $fund->description }} @endif</option>
                    @endforeach
                </select>
            </div>

            <!-- Donor Information -->
            <div class="border-t border-gray-200 pt-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Donor Details</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="donor_name" class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="donor_name" wire:model="donor_name" name="donor_name" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Address (for Receipt) <span class="text-red-500">*</span></label>
                        <input type="email" id="email" wire:model="email" name="email" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Mobile Phone (Optional)</label>
                        <input type="tel" id="phone" wire:model="phone" name="phone" placeholder="e.g. 0244123456" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">Notes / Prayer Request (Optional)</label>
                        <input type="text" id="notes" wire:model="notes" name="notes" placeholder="Special instructions..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="is_anonymous" name="is_anonymous" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-xs font-medium text-gray-700">Make this contribution anonymous (hide name on church public reports)</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 font-bold text-base transition">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Proceed to Pay with Paystack</span>
                </button>
                <div class="flex items-center justify-center gap-2 mt-3 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>256-bit SSL encrypted. Supports Cards, Mobile Money &amp; Bank Transfer via Paystack.</span>
                </div>
            </div>
        </form>
    </div>
</div>
