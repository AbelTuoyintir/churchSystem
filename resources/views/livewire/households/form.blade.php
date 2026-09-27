<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $household && $household->exists ? 'Edit Household: ' . $household->name : 'Create Household' }}
            </h2>
            <a href="{{ route('households.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Household Name *</label>
                    <input id="name" type="text" wire:model="name" placeholder="e.g. The Smith Family" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Household Phone</label>
                    <input id="phone" type="text" wire:model="phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    @error('phone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
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

            @can('update', $household)
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition">
                        Save Household
                    </button>
                </div>
            @endcan
        </form>

        <!-- Household Members Section (Visible when editing an existing household) -->
        @if ($household && $household->exists)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Household Members</h3>

                @if (session()->has('member_success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md text-sm">
                        {{ session('member_success') }}
                    </div>
                @endif

                <!-- Members List Table -->
                <div class="mb-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($attachedMembers as $person)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-medium">
                                        <a href="{{ route('people.edit', $person) }}" class="text-indigo-600 hover:underline">
                                            {{ $person->first_name }} {{ $person->last_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 capitalize">
                                        {{ $person->pivot->role }}
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm">
                                        @can('update', $household)
                                            <button type="button" wire:click="confirmDetachMember({{ $person->id }})" class="text-red-600 hover:text-red-900">
                                                Detach
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No members in this household yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Attach Member Form -->
                @can('update', $household)
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Attach Member</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="selectedPersonId" class="block text-xs font-medium text-gray-700 mb-1">Select Person</label>
                                <select id="selectedPersonId" wire:model="selectedPersonId" class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                    <option value="">-- Choose Person --</option>
                                    @foreach ($allPeople as $p)
                                        <option value="{{ $p->id }}">{{ $p->last_name }}, {{ $p->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedPersonId') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="memberRole" class="block text-xs font-medium text-gray-700 mb-1">Role in Household</label>
                                <select id="memberRole" wire:model="memberRole" class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                    <option value="head">Head</option>
                                    <option value="spouse">Spouse</option>
                                    <option value="child">Child</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('memberRole') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <button type="button" wire:click="attachMember" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Attach Member
                                </button>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        @endif
    </div>

    <!-- Confirm Member Detach Modal -->
    @if ($confirmingMemberDetach)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Detach Member</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to remove this person from the household?</p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('confirmingMemberDetach', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button type="button" wire:click="detachMember" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md">Detach Member</button>
                </div>
            </div>
        </div>
    @endif
</div>
