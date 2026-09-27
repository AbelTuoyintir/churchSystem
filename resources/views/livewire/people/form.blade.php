<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $person && $person->exists ? 'Edit Person: ' . $person->first_name . ' ' . $person->last_name : 'Create Person' }}
            </h2>
            <a href="{{ route('people.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md text-sm">
                    <p class="font-bold">Please correct the errors below:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Basic Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input id="first_name" type="text" wire:model="first_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('first_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input id="middle_name" type="text" wire:model="middle_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('middle_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input id="last_name" type="text" wire:model="last_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('last_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="preferred_name" class="block text-sm font-medium text-gray-700 mb-1">Preferred Name</label>
                    <input id="preferred_name" type="text" wire:model="preferred_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('preferred_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" wire:model="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input id="phone" type="text" wire:model="phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('phone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="alternate_phone" class="block text-sm font-medium text-gray-700 mb-1">Alternate Phone</label>
                    <input id="alternate_phone" type="text" wire:model="alternate_phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('alternate_phone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input id="date_of_birth" type="date" wire:model="date_of_birth" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('date_of_birth') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <input id="gender" type="text" wire:model="gender" placeholder="e.g. Male / Female" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('gender') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                    <input id="marital_status" type="text" wire:model="marital_status" placeholder="e.g. Single, Married, Widowed" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('marital_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="membership_status" class="block text-sm font-medium text-gray-700 mb-1">Membership Status *</label>
                    <select id="membership_status" wire:model="membership_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        <option value="visitor">Visitor</option>
                        <option value="member">Member</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('membership_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="joined_at" class="block text-sm font-medium text-gray-700 mb-1">Joined At</label>
                    <input id="joined_at" type="date" wire:model="joined_at" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('joined_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="baptized_at" class="block text-sm font-medium text-gray-700 mb-1">Baptized At</label>
                    <input id="baptized_at" type="date" wire:model="baptized_at" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('baptized_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="photo_path" class="block text-sm font-medium text-gray-700 mb-1">Photo Path</label>
                    <input id="photo_path" type="text" wire:model="photo_path" placeholder="Path or URL" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('photo_path') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Address Fieldset -->
            <fieldset class="border border-gray-300 rounded-md p-4 mb-4">
                <legend class="text-sm font-semibold text-gray-700 px-2">Address Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                        <input id="address_line1" type="text" wire:model="address_line1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('address_line1') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                        <input id="address_line2" type="text" wire:model="address_line2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('address_line2') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input id="city" type="text" wire:model="city" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('city') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input id="state" type="text" wire:model="state" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('state') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                        <input id="postal_code" type="text" wire:model="postal_code" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('postal_code') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input id="country" type="text" wire:model="country" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('country') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </fieldset>

            <!-- Checkboxes / Toggles -->
            <div class="flex flex-wrap gap-6 mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="email_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Email Opt In</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="sms_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">SMS Opt In</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Is Active</span>
                </label>
            </div>

            <!-- Pastoral Notes (Sensitive Data: Admin/Staff only) -->
            @can('viewNotes', $person)
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Pastoral Notes (Sensitive)</label>
                    <textarea id="notes" wire:model="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border"></textarea>
                    @error('notes') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            @endcan

            @can('update', $person)
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition">
                        Save Person
                    </button>
                </div>
            @endcan
        </form>

        <!-- Households Relation Panel (Visible when editing an existing person) -->
        @if ($person && $person->exists)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Households</h3>

                @if (session()->has('household_success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md text-sm">
                        {{ session('household_success') }}
                    </div>
                @endif

                <!-- Attached Households List -->
                <div class="mb-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Household Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($attachedHouseholds as $household)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-medium">
                                        <a href="{{ route('households.edit', $household) }}" class="text-indigo-600 hover:underline">
                                            {{ $household->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 capitalize">
                                        {{ $household->pivot->role }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm">
                                        @can('update', $person)
                                            <button type="button" wire:click="confirmDetachHousehold({{ $household->id }})" class="text-red-600 hover:text-red-900">
                                                Detach
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No households attached yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Attach Household Form -->
                @can('update', $person)
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Attach to Household</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="selectedHouseholdId" class="block text-xs font-medium text-gray-700 mb-1">Select Household</label>
                                <select id="selectedHouseholdId" wire:model="selectedHouseholdId" class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                    <option value="">-- Choose Household --</option>
                                    @foreach ($allHouseholds as $hh)
                                        <option value="{{ $hh->id }}">{{ $hh->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedHouseholdId') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="householdRole" class="block text-xs font-medium text-gray-700 mb-1">Role in Household</label>
                                <select id="householdRole" wire:model="householdRole" class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                    <option value="head">Head</option>
                                    <option value="spouse">Spouse</option>
                                    <option value="child">Child</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('householdRole') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <button type="button" wire:click="attachHousehold" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Attach
                                </button>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        @endif
    </div>

    <!-- Confirm Household Detach Modal -->
    @if ($confirmingHouseholdDetach)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Detach</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to detach this household from the person?</p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('confirmingHouseholdDetach', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button type="button" wire:click="detachHousehold" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md">Detach Household</button>
                </div>
            </div>
        </div>
    @endif
</div>
