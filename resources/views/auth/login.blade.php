
{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GraceManage') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased">
    {{-- resources/views/auth/login.blade.php --}}

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-[#faf9f7]">

        {{-- ============================================================ --}}
        {{-- LEFT PANEL: Branded visual (hidden on mobile)                --}}
        {{-- ============================================================ --}}
        <div class="hidden lg:flex relative flex-col justify-between p-12 overflow-hidden
                    bg-[radial-gradient(circle_at_20%_20%,#2b5e4a_0%,#1e2f2a_70%)] text-white">

            {{-- Decorative SVG blobs --}}
            {{-- <div class="absolute inset-0 opacity-30 pointer-events-none">
                <svg class="absolute -top-20 -left-20 w-96 h-96" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="180" fill="#d9e9e1" fill-opacity="0.15"/>
                </svg>
                <svg class="absolute bottom-0 right-0 w-[500px] h-[500px]" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="200" fill="#f5e7d9" fill-opacity="0.08"/>
                </svg>
                <svg class="absolute top-1/2 left-1/3 w-64 h-64" viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="100" fill="#aacbbd" fill-opacity="0.1"/>
                </svg>
            </div> --}}

            {{-- Logo --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-brand-cream flex items-center justify-center text-brand text-xl">
                    <i class="fas fa-church"></i>
                </div>
                <span class="font-display font-bold text-2xl tracking-tight">
                    Grace<span class="text-brand-pale font-semibold">Manage</span>
                </span>
            </div>

            {{-- Hero content --}}
            <div class="relative z-10 max-w-md">
                <h2 class="font-display font-bold text-4xl leading-tight tracking-tight mb-6">
                    Welcome back to your community.
                </h2>
                <p class="text-brand-pale/90 text-lg leading-relaxed mb-8">
                    Manage members, events, giving, and volunteers — all in one place.
                    Sign in to continue shepherding your congregation with grace.
                </p>

                {{-- Feature bullets --}}
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>Trusted by 2,400+ churches worldwide</span>
                    </li>
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>Bank-level security for donations</span>
                    </li>
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>24/7 support from real people</span>
                    </li>
                </ul>
            </div>

            {{-- Footer quote --}}
            <div class="relative z-10 border-t border-white/10 pt-6">
                <p class="text-sm text-brand-pale/70 italic">
                    "For where two or three gather in my name, there am I with them."
                </p>
                <p class="text-xs text-brand-pale/50 mt-2 tracking-wide uppercase">
                    — Matthew 18:20
                </p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT PANEL: Login form                                     --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col justify-center px-6 sm:px-10 lg:px-16 py-12">

            {{-- Mobile logo (shown only on small screens) --}}
            <div class="lg:hidden flex items-center justify-center gap-3 mb-10">
                <div class="w-11 h-11 rounded-xl bg-brand flex items-center justify-center text-brand-cream text-xl">
                    <i class="fas fa-church"></i>
                </div>
                <span class="font-display font-bold text-2xl tracking-tight text-brand-dark">
                    Grace<span class="text-brand-light font-semibold">Manage</span>
                </span>
            </div>

            {{-- Form wrapper --}}
            <div class="w-full max-w-md mx-auto">

                {{-- Heading --}}
                <div class="mb-8">
                    <h1 class="font-display font-bold text-3xl sm:text-4xl tracking-tight text-[#16231f]">
                        Sign in
                    </h1>
                    <p class="mt-2 text-brand-muted">
                        Welcome back — let's get you connected.
                    </p>
                </div>

                {{-- Session status --}}
                <x-auth-session-status
                    class="mb-6 rounded-xl bg-brand-pale border border-brand-light/30 px-4 py-3 text-sm text-brand-dark"
                    :status="session('status')"
                />

                {{-- Login form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <x-input-label
                            for="email"
                            :value="__('Email address')"
                            class="text-[#2e3a35] font-medium text-sm"
                        />
                        <div class="relative mt-1.5">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-brand-muted/70">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <x-text-input
                                id="email"
                                class="block w-full rounded-xl border-[#dfe6e1] bg-white pl-11 pr-4 py-3
                                       text-[#1e1e2a] shadow-sm placeholder:text-brand-muted/60
                                       focus:border-brand focus:ring focus:ring-brand/30 focus:ring-opacity-50
                                       transition"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@example.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label
                                for="password"
                                :value="__('Password')"
                                class="text-[#2e3a35] font-medium text-sm"
                            />
                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-sm font-medium text-brand-light underline-offset-2
                                           hover:text-brand hover:underline transition
                                           focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2 rounded"
                                >
                                    {{ __('Forgot?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative mt-1.5">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-brand-muted/70">
                                <i class="fas fa-lock"></i>
                            </span>
                            <x-text-input
                                id="password"
                                class="block w-full rounded-xl border-[#dfe6e1] bg-white pl-11 pr-11 py-3
                                       text-[#1e1e2a] shadow-sm placeholder:text-brand-muted/60
                                       focus:border-brand focus:ring focus:ring-brand/30 focus:ring-opacity-50
                                       transition"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            {{-- Show/hide toggle (optional, needs JS) --}}
                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-muted/70 hover:text-brand transition"
                                aria-label="Toggle password visibility"
                            >
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input
                                id="remember_me"
                                type="checkbox"
                                class="rounded border-[#c6ddd2] text-brand shadow-sm
                                       focus:ring-brand/50 focus:ring-offset-0"
                                name="remember"
                            >
                            <span class="ms-2 text-sm text-brand-muted">{{ __('Keep me signed in') }}</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-1">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   bg-brand hover:bg-[#1f4738] active:bg-[#1a3d31]
                                   text-white font-semibold text-base px-6 py-3.5 rounded-full
                                   shadow-lg shadow-brand/25 hover:-translate-y-px
                                   focus:outline-none focus:ring-2 focus:ring-brand/50 focus:ring-offset-2
                                   transition-all duration-200"
                        >
                            
                            {{ __('Sign in') }}
                        </button>
                    </div>

                    {{-- Divider --}}
                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-[#e2eae5]"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-[#faf9f7] px-4 text-xs uppercase tracking-wider text-brand-muted">
                                or
                            </span>
                        </div>
                    </div>

                    {{-- Register CTA --}}
                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="w-full inline-flex items-center justify-center gap-2
                                   border-[1.5px] border-brand text-brand font-semibold text-base
                                   px-6 py-3.5 rounded-full hover:bg-brand/5
                                   focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2
                                   transition-all duration-200"
                        >
                            <i class="fas fa-user-plus"></i>
                            {{ __('Create a new account') }}
                        </a>
                    @endif
                </form>

                {{-- Footer links --}}
                <div class="mt-10 flex items-center justify-center gap-6 text-xs text-brand-muted">
                    <a href="#" class="hover:text-brand transition">Privacy Policy</a>
                    <span class="w-1 h-1 rounded-full bg-brand-muted/40"></span>
                    <a href="#" class="hover:text-brand transition">Terms of Service</a>
                    <span class="w-1 h-1 rounded-full bg-brand-muted/40"></span>
                    <a href="#" class="hover:text-brand transition">Help</a>
                </div>

                {{-- Copyright --}}
                <p class="mt-4 text-center text-xs text-brand-muted/70">
                    &copy; {{ date('Y') }} GraceManage. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Password toggle script (vanilla JS, no dependencies)         --}}
    {{-- ============================================================ --}}
    <script>
        (function () {
            const toggle = document.getElementById('togglePassword');
            const input  = document.getElementById('password');
            const icon   = document.getElementById('eyeIcon');
            if (!toggle || !input || !icon) return;

            toggle.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            });
        })();
    </script>

</body>
</html>