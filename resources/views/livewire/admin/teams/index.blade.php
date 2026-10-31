<div class="space-y-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">{{ $editingTeamId ? 'Edit team' : 'Create team' }}</h2>
                <p class="mt-1 text-sm text-gray-500">Manage church-wide ministry teams and leadership.</p>
            </div>
            <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="team-name" class="mb-1 block text-sm font-medium text-gray-700">Team name</label>
                    <input id="team-name" wire:model="name" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="team-leader" class="mb-1 block text-sm font-medium text-gray-700">Leader</label>
                    <select id="team-leader" wire:model="leaderId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">No leader assigned</option>
                        @foreach ($people as $person)
                            <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                        @endforeach
                    </select>
                    @error('leaderId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="team-group" class="mb-1 block text-sm font-medium text-gray-700">Linked group</label>
                    <select id="team-group" wire:model="groupId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">No linked group</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('groupId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 pb-2 text-sm text-gray-700"><input type="checkbox" wire:model="isActive" class="rounded border-gray-300 text-indigo-700 focus:ring-indigo-600">Active team</label>
                </div>
                <div class="md:col-span-2">
                    <label for="team-description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="team-description" wire:model="description" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-2 md:col-span-2">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:opacity-60">{{ $editingTeamId ? 'Save changes' : 'Create team' }}</button>
                    @if ($editingTeamId)
                        <button type="button" wire:click="resetForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    @endif
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-semibold text-gray-900">Teams</h2>
                <label for="team-search" class="sr-only">Search teams</label>
                <input id="team-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search teams" class="w-full rounded-md border-gray-300 text-sm shadow-sm sm:max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"><tr><th class="px-5 py-3">Team</th><th class="px-5 py-3">Leader</th><th class="px-5 py-3">Group</th><th class="px-5 py-3">Members</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($teams as $team)
                            <tr wire:key="admin-team-{{ $team->id }}">
                                <td class="px-5 py-3"><p class="font-medium text-gray-800">{{ $team->name }}</p><p class="mt-1 text-xs text-gray-500">{{ $team->description }}</p></td>
                                <td class="px-5 py-3 text-gray-600">{{ $team->leader?->full_name ?? 'Unassigned' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $team->group?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $team->members_count }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $team->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right"><button type="button" wire:click="edit({{ $team->id }})" class="font-medium text-indigo-700 hover:text-indigo-900">Edit</button><button type="button" wire:click="delete({{ $team->id }})" wire:confirm="Delete this team?" class="ml-3 font-medium text-red-700 hover:text-red-900">Delete</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No teams found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($teams->hasPages())<div class="border-t border-gray-200 px-5 py-4">{{ $teams->links() }}</div>@endif
        </section>
    </div>
