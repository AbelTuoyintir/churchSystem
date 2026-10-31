<div>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">User Role Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage account access for members, pastors, stewards, and church staff.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Church accounts</h3>
                    <p class="mt-1 text-sm text-gray-500">Role changes take effect immediately.</p>
                </div>

                <div class="w-full sm:max-w-sm">
                    <label for="user-search" class="sr-only">Search users</label>
                    <input
                        id="user-search"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name or email"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">User</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Current role</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Change role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($users as $user)
                            @php
                                $currentRole = $user->role ?? $user->roles->first()?->name ?? 'Unassigned';
                            @endphp
                            <tr wire:key="user-role-{{ $user->id }}">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="mt-1 text-sm text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                        {{ $roleOptions[$currentRole] ?? $currentRole }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if (auth()->id() === $user->id)
                                        <span class="text-sm text-gray-500">Your account</span>
                                    @else
                                        <div class="flex min-w-64 items-start gap-2">
                                            <div class="min-w-0 flex-1">
                                                <label for="role-{{ $user->id }}" class="sr-only">Role for {{ $user->name }}</label>
                                                <select
                                                    id="role-{{ $user->id }}"
                                                    wire:model="selectedRoles.{{ $user->id }}"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="" disabled>Select a role</option>
                                                    @foreach ($roleOptions as $roleName => $roleLabel)
                                                        <option value="{{ $roleName }}">{{ $roleLabel }}</option>
                                                    @endforeach
                                                </select>
                                                @error('selectedRoles.'.$user->id)
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <button
                                                type="button"
                                                wire:click="updateRole({{ $user->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="updateRole({{ $user->id }})"
                                                class="shrink-0 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-wait disabled:opacity-60"
                                            >
                                                Save
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">No user accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
