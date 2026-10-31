<div class="mx-auto max-w-6xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <p class="text-sm font-semibold uppercase text-emerald-700">Member space</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">My Household</h1>
            <p class="mt-1 text-sm text-gray-600">Shared contact details and household members.</p>
        </div>
        <a href="{{ route('members.portal') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Back to my profile</a>
    </header>

    @if (session()->has('success'))
        <div role="status" class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    @if ($households->isEmpty())
        <div class="border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold text-gray-900">No household linked</h2>
            <p class="mt-2 text-sm text-gray-600">There is no household associated with your member record.</p>
        </div>
    @else
        @if ($households->count() > 1)
            <nav aria-label="Your households" class="flex flex-wrap gap-2 border-b border-gray-200">
                @foreach ($households as $household)
                    <button type="button" wire:click="selectHousehold({{ $household->id }})" @class([
                        'border-b-2 px-3 py-2 text-sm font-semibold',
                        'border-indigo-700 text-indigo-800' => $selectedHousehold?->id === $household->id,
                        'border-transparent text-gray-600 hover:text-gray-900' => $selectedHousehold?->id !== $household->id,
                    ])>
                        {{ $household->name }}
                    </button>
                @endforeach
            </nav>
        @endif

        @if ($selectedHousehold)
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)]">
                <section class="space-y-5">
                    <div class="flex items-baseline justify-between gap-4 border-b border-gray-200 pb-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-500">Household</p>
                            <h2 class="mt-1 text-xl font-bold text-gray-900">{{ $selectedHousehold->name }}</h2>
                        </div>
                        <span class="text-sm text-gray-500">{{ $selectedHousehold->people->count() }} {{ \Illuminate\Support\Str::plural('member', $selectedHousehold->people->count()) }}</span>
                    </div>

                    <form wire:submit="updateHousehold" class="space-y-4">
                        <div>
                            <label for="address_line1" class="block text-sm font-medium text-gray-700">Address line 1</label>
                            <input id="address_line1" wire:model="address_line1" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                            @error('address_line1') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="address_line2" class="block text-sm font-medium text-gray-700">Address line 2</label>
                            <input id="address_line2" wire:model="address_line2" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                            @error('address_line2') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input id="city" wire:model="city" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                                @error('city') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700">State / region</label>
                                <input id="state" wire:model="state" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                                @error('state') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal code</label>
                                <input id="postal_code" wire:model="postal_code" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                                @error('postal_code') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                <input id="country" wire:model="country" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                                @error('country') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Household phone</label>
                            <input id="phone" type="tel" wire:model="phone" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                            @error('phone') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <button type="submit" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-700" wire:loading.attr="disabled">Save shared details</button>
                        </div>
                    </form>
                </section>

                <aside class="border-l border-gray-200 pl-0 lg:pl-8">
                    <h2 class="text-xs font-semibold uppercase text-gray-500">Household members</h2>
                    <ul class="mt-3 divide-y divide-gray-200">
                        @foreach ($selectedHousehold->people as $person)
                            <li wire:key="household-person-{{ $person->id }}" class="flex items-center justify-between gap-4 py-3">
                                <span class="font-medium text-gray-900">{{ $person->full_name }}</span>
                                <span class="text-sm capitalize text-gray-600">{{ str_replace('_', ' ', $person->pivot->role ?: 'member') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            </div>
        @endif
    @endif
</div>