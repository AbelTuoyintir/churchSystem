<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                People
            </h2>
            @can('create', App\Models\Person::class)
                <a href="{{ route('people.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                    + Add Person
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if ($generatedPasswordNotice)
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-800 rounded-md flex justify-between items-center">
                <div>
                    <strong class="font-bold">Generated Credentials:</strong>
                    <span class="ml-2 font-mono text-sm bg-white px-2 py-1 rounded border">{{ $generatedPasswordNotice }}</span>
                </div>
                <button wire:click="$set('generatedPasswordNotice', null)" aria-label="Dismiss notice" class="text-blue-600 hover:text-blue-800 font-bold">&times;</button>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input id="search" type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, email, phone..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
            </div>

            <div>
                <label for="membershipStatus" class="block text-sm font-medium text-gray-700 mb-1">Membership Status</label>
                <select id="membershipStatus" wire:model.live="membershipStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    <option value="">All Statuses</option>
                    <option value="visitor">Visitor</option>
                    <option value="member">Member</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">Active Status</label>
                <select id="isActive" wire:model.live="isActive" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    <option value="">All</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive Only</option>
                </select>
            </div>
        </div>

        <!-- People Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membership Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($people as $person)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $person->first_name }} {{ $person->middle_name ? $person->middle_name . ' ' : '' }}{{ $person->last_name }}
                                @if($person->preferred_name)
                                    <span class="text-xs text-gray-500">("{{ $person->preferred_name }}")</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if (! $limitedDirectory || $fullContactPersonIds->contains($person->id))
                                    {{ $person->email ?: '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if (! $limitedDirectory || $fullContactPersonIds->contains($person->id))
                                    {{ $person->phone ?: '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($person->membership_status === 'member')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Member
                                    </span>
                                @elseif ($person->membership_status === 'visitor')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Visitor
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $person->joined_at ? $person->joined_at->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @can('generatePassword', $person)
                                    <button wire:click="confirmGeneratePassword({{ $person->id }})" title="Generate & Send Password" aria-label="Generate & Send Password" class="text-emerald-600 hover:text-emerald-900 font-semibold">
                                        🔑 Pass
                                    </button>
                                @endcan

                                @can('update', $person)
                                    <a href="{{ route('people.edit', $person) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @elseif (! $limitedDirectory && auth()->user()->can('view', $person))
                                    <a href="{{ route('people.edit', $person) }}" class="text-gray-600 hover:text-gray-900">View</a>
                                @endcan

                                @can('delete', $person)
                                    <button wire:click="confirmDelete({{ $person->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No people found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $people->links() }}
        </div>
    </div>

    <!-- Confirmation Modal for Password Generation -->
    @if ($confirmingPasswordGeneration)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="generate-password-modal-title">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 id="generate-password-modal-title" class="text-lg font-bold text-gray-900 mb-2">Generate & Send Password</h3>
                <p class="text-sm text-gray-600 mb-4">
                    This will generate a unique secure login password for this person, update/create their user account, and send credentials.
                </p>

                <div class="mb-6">
                    <label for="passwordChannel" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Delivery Channel</label>
                    <select id="passwordChannel" wire:model="passwordChannel" class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="auto">Auto (Preferred contact method)</option>
                        <option value="email">Email Only</option>
                        <option value="sms">SMS Only</option>
                        <option value="both">Both Email & SMS</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('confirmingPasswordGeneration', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button type="button" wire:click="generatePassword" wire:loading.attr="disabled" wire:target="generatePassword" class="inline-flex items-center px-4 py-2 text-sm text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 rounded-md font-semibold transition">
                        <svg wire:loading wire:target="generatePassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Generate &amp; Send</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Deletion Confirmation Modal -->
    @if ($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="delete-person-modal-title">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 id="delete-person-modal-title" class="text-lg font-bold text-gray-900 mb-2">Confirm Deletion</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this person? This action can be undone via soft delete.</p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingDeletion', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button wire:click="deletePerson" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md">Delete Person</button>
                </div>
            </div>
        </div>
    @endif
</div>
