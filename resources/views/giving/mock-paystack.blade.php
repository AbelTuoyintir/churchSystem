<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Checkout (Test Mode)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl shadow-xl overflow-hidden border border-slate-200">
        <!-- Paystack Brand Header -->
        <div class="bg-[#0ba4db] text-white p-6 text-center">
            <div class="inline-flex items-center gap-2 mb-2">
                <span class="font-extrabold text-2xl tracking-tight">paystack</span>
                <span class="text-[10px] bg-white/20 uppercase tracking-widest px-2 py-0.5 rounded font-bold">Sandbox</span>
            </div>
            <p class="text-xs text-sky-100">Secured payment for {{ config('app.name', 'Church System') }}</p>
        </div>

        <div class="p-6 space-y-6">
            <div class="text-center pb-4 border-b border-slate-100">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Due</span>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">
                    {{ $currency }} {{ number_format($pending['amount'] ?? 100, 2) }}
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    Paying as: {{ $pending['email'] ?? 'donor@example.com' }}
                </div>
            </div>

            <!-- Simulated Card / Mobile Money Options -->
            <div class="space-y-3">
                <div class="p-3 border border-sky-300 bg-sky-50/50 rounded-lg flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 font-medium text-slate-800">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Card / Mobile Money (Test Mode)</span>
                    </div>
                    <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded">Ready</span>
                </div>
            </div>

            <!-- Simulation Action Buttons -->
            <div class="space-y-3 pt-2">
                <a href="{{ route('payment.callback', ['reference' => $reference]) }}" class="w-full block py-3 px-4 bg-[#0ba4db] hover:bg-[#098ec0] text-white font-bold rounded-xl text-center shadow-sm transition">
                    Simulate Successful Payment
                </a>

                <a href="{{ route('giving.pay') }}" class="w-full block py-2.5 px-4 text-center text-sm font-semibold text-slate-500 hover:text-slate-800">
                    Cancel and Return
                </a>
            </div>

            <p class="text-[11px] text-center text-slate-400">
                Test environment simulator. No real money will be charged.
            </p>
        </div>
    </div>
</body>
</html>
