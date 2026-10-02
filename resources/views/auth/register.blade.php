<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GraceManage') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#1e2f2a',
                            DEFAULT: '#2b5e4a',
                            light: '#3e7a63',
                            pale: '#d9e9e1',
                            cream: '#f5e7d9',
                            muted: '#5b6d64',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-[#1e1e2a]">
  
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-[#faf9f7]">

        {{-- ============================================================ --}}
        {{-- LEFT PANEL: Branded visual (hidden on mobile)                --}}
        {{-- ============================================================ --}}
        <div class="hidden lg:flex relative flex-col justify-between p-12 overflow-hidden
                    bg-[radial-gradient(circle_at_20%_20%,#2b5e4a_0%,#1e2f2a_70%)] text-white">

            {{-- Decorative SVG blobs --}}
            <div class="absolute inset-0 opacity-30 pointer-events-none">
                <svg class="absolute -top-20 -left-20 w-96 h-96" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="180" fill="#d9e9e1" fill-opacity="0.15"/>
                </svg>
                <svg class="absolute bottom-0 right-0 w-[500px] h-[500px]" viewBox="0 0 400 400" fill="none">
                    <circle cx="200" cy="200" r="200" fill="#f5e7d9" fill-opacity="0.08"/>
                </svg>
                <svg class="absolute top-1/2 left-1/3 w-64 h-64" viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="100" fill="#aacbbd" fill-opacity="0.1"/>
                </svg>
            </div>

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
                    Join a growing community of churches.
                </h2>
                <p class="text-brand-pale/90 text-lg leading-relaxed mb-8">
                    Create your account and start managing members, events, giving,
                    and volunteers — all from one place.
                </p>

                {{-- Feature bullets --}}
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>Free 30-day trial, no credit card required</span>
                    </li>
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>Unlimited members on every plan</span>
                    </li>
                    <li class="flex items-center gap-3 text-brand-pale/90">
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span>Cancel anytime, keep your data</span>
                    </li>
                </ul>
            </div>

            {{-- Footer quote --}}
            <div class="relative z-10 border-t border-white/10 pt-6">
                <p class="text-sm text-brand-pale/70 italic">
                    "Unless the Lord builds the house, the builders labor in vain."
                </p>
                <p class="text-xs text-brand-pale/50 mt-2 tracking-wide uppercase">
                    — Psalm 127:1
                </p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT PANEL: Register form                                   --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col justify-center px-6 sm:px-10 lg:px-16 py-12">

            {{-- Mobile logo --}}
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
                        Create your account
                    </h1>
                    <p class="mt-2 text-brand-muted">
                        Get started in less than a minute.
                    </p>
                </div>

                {{-- Session status --}}
                <x-auth-session-status
                    class="mb-6 rounded-xl bg-brand-pale border border-brand-light/30 px-4 py-3 text-sm text-brand-dark"
                    :status="session('status')"
                />

                {{-- Register form --}}
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" :value="__('Full name') . ' *'" class="text-[#2e3a35] font-medium text-sm" />
                        <div class="relative mt-1.5">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-brand-muted/70">
                                <i class="fas fa-user"></i>
                            </span>
                            <x-text-input
                                id="name"
                                class="block w-full rounded-xl border-[#dfe6e1] bg-white pl-11 pr-4 py-3
                                       text-[#1e1e2a] shadow-sm placeholder:text-brand-muted/60
                                       focus:border-brand focus:ring focus:ring-brand/30 focus:ring-opacity-50
                                       transition"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="e.g. John Doe"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email address') . ' *'" class="text-[#2e3a35] font-medium text-sm" />
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
                                autocomplete="username"
                                placeholder="you@example.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password') . ' *'" class="text-[#2e3a35] font-medium text-sm" />
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
                                autocomplete="new-password"
                                placeholder="Minimum 8 characters"
                            />
                            <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-muted/70 hover:text-brand transition"
                                    aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm password') . ' *'" class="text-[#2e3a35] font-medium text-sm" />
                        <div class="relative mt-1.5">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-brand-muted/70">
                                <i class="fas fa-lock"></i>
                            </span>
                            <x-text-input
                                id="password_confirmation"
                                class="block w-full rounded-xl border-[#dfe6e1] bg-white pl-11 pr-11 py-3
                                       text-[#1e1e2a] shadow-sm placeholder:text-brand-muted/60
                                       focus:border-brand focus:ring focus:ring-brand/30 focus:ring-opacity-50
                                       transition"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter password"
                            />
                            <button type="button" id="togglePasswordConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-muted/70 hover:text-brand transition"
                                    aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="eyeIconConfirm"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-start gap-2.5">
                        <input
                            id="terms"
                            type="checkbox"
                            name="terms"
                            required
                            class="mt-0.5 rounded border-[#c6ddd2] text-brand shadow-sm
                                   focus:ring-brand/50 focus:ring-offset-0"
                        >
                        <label for="terms" class="text-sm text-brand-muted cursor-pointer leading-snug">
                            I agree to the
                            <a href="#" class="font-medium text-brand-light underline-offset-2 hover:text-brand hover:underline">Terms of Service</a>
                            and
                            <a href="#" class="font-medium text-brand-light underline-offset-2 hover:text-brand hover:underline">Privacy Policy</a>.
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('terms')" class="mt-2 text-sm text-red-600" />

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
                            <i class="fas fa-user-plus"></i>
                            {{ __('Create account') }}
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

                    {{-- Login CTA --}}
                    <a
                        href="{{ route('login') }}"
                        class="w-full inline-flex items-center justify-center gap-2
                               border-[1.5px] border-brand text-brand font-semibold text-base
                               px-6 py-3.5 rounded-full hover:bg-brand/5
                               focus:outline-none focus:ring-2 focus:ring-brand/40 focus:ring-offset-2
                               transition-all duration-200"
                    >
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        {{ __('Sign in to existing account') }}
                    </a>
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
    {{-- Password visibility toggle scripts                          --}}
    {{-- ============================================================ --}}
    <script>
        function bindPasswordToggle(toggleId, inputId, iconId) {
            const toggle = document.getElementById(toggleId);
            const input  = document.getElementById(inputId);
            const icon   = document.getElementById(iconId);
            if (!toggle || !input || !icon) return;

            toggle.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        bindPasswordToggle('togglePassword', 'password', 'eyeIcon');
        bindPasswordToggle('togglePasswordConfirm', 'password_confirmation', 'eyeIconConfirm');
    </script>

</body>
</html>