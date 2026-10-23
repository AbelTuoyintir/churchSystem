@extends('layouts.app', ['header' => 'Payment Receipt'])

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 sm:px-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden print:shadow-none print:border-none">
        <!-- Top Status Banner -->
        <div class="bg-emerald-600 text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-black tracking-tight">Payment Successful!</h2>
            <p class="text-emerald-100 text-sm mt-1">Thank you for your generosity and support for the ministry.</p>
        </div>

        <!-- Receipt Body -->
        <div class="p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-gray-100 gap-2">
                <div>
                    <span class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Amount Paid</span>
                    <div class="text-3xl font-extrabold text-gray-900 mt-0.5">
                        {{ $currency }} {{ number_format($contribution->amount, 2) }}
                    </div>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Reference No.</span>
                    <div class="text-sm font-mono font-medium text-gray-700 mt-0.5">
                        {{ $contribution->reference }}
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="p-3.5 bg-gray-50 rounded-lg">
                    <span class="block text-xs font-semibold text-gray-500 uppercase">Donor</span>
                    <span class="font-medium text-gray-900">
                        {{ $contribution->is_anonymous ? 'Anonymous Donor' : ($contribution->person?->full_name ?? auth()->user()?->name ?? 'Guest') }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-lg">
                    <span class="block text-xs font-semibold text-gray-500 uppercase">Designation / Fund</span>
                    <span class="font-medium text-gray-900">
                        {{ $contribution->fund?->name ?? 'General Fund' }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-lg">
                    <span class="block text-xs font-semibold text-gray-500 uppercase">Payment Method</span>
                    <span class="font-medium text-gray-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        Paystack Online
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-lg">
                    <span class="block text-xs font-semibold text-gray-500 uppercase">Date &amp; Time</span>
                    <span class="font-medium text-gray-900">
                        {{ $contribution->created_at ? $contribution->created_at->format('M d, Y - h:i A') : now()->format('M d, Y') }}
                    </span>
                </div>
            </div>

            @if($contribution->note)
                <div class="p-3.5 bg-gray-50 rounded-lg text-sm">
                    <span class="block text-xs font-semibold text-gray-500 uppercase">Category / Note</span>
                    <span class="font-medium text-gray-800">{{ $contribution->note }}</span>
                </div>
            @endif

            @if($contribution->pledge)
                <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-lg text-sm space-y-2">
                    <div class="font-bold text-indigo-900">Pledge Progress Updated</div>
                    <p class="text-xs text-indigo-700">
                        This contribution has been credited toward your pledge of {{ $currency }} {{ number_format($contribution->pledge->amount, 2) }}.
                    </p>
                    <div class="w-full bg-indigo-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $contribution->pledge->progress_percent }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-indigo-800 font-semibold">
                        <span>Fulfilled: {{ $currency }} {{ number_format($contribution->pledge->fulfilled_amount, 2) }}</span>
                        <span>{{ $contribution->pledge->progress_percent }}%</span>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="pt-4 flex flex-col sm:flex-row gap-3 print:hidden">
                <button type="button" onclick="window.print()" class="w-full sm:flex-1 py-2.5 px-4 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Receipt
                </button>

                <a href="{{ route('giving.my') }}" class="w-full sm:flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm text-center flex items-center justify-center gap-2">
                    View My Giving History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
