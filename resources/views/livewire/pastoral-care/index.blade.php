<div class="mx-auto max-w-7xl space-y-6">
    @if (session()->has('success'))
        <div class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-5">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-rose-700">Pastoral Care</p>
            <h1 class="mt-1 text-2xl font-bold text-stone-900">Shepherding &amp; Care</h1>
            <p class="mt-1 text-sm text-stone-600">Private care notes, visitation log, follow-up tasks, and counselling records.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search notes..."
                   class="rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            <select wire:model.live="typeFilter" class="rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                <option value="all">All types</option>
                <option value="note">Care notes</option>
                <option value="visit">Visits</option>
                <option value="followup">Follow-ups</option>
                <option value="counselling">Counselling</option>
            </select>
            <select wire:model.live="statusFilter" class="rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                <option value="all">All statuses</option>
                <option value="open">Open</option>
                <option value="in_progress">In progress</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </header>

    <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-stone-900">{{ $editingId ? 'Edit entry' : 'New care entry' }}</h2>
            <form wire:submit="save" class="mt-4 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Person</label>
                        <select wire:model="person_id" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Select person</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}">{{ $person->first_name }} {{ $person->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Type</label>
                        <select wire:model="type" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="note">Care note</option>
                            <option value="visit">Visitation</option>
                            <option value="followup">Follow-up task</option>
                            <option value="counselling">Counselling</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Title</label>
                    <input type="text" wire:model="title" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notes</label>
                    <textarea wire:model="body" rows="5" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                    @error('body') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Status</label>
                        <select wire:model="status" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="open">Open</option>
                            <option value="in_progress">In progress</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Priority</label>
                        <select wire:model="priority" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Due date</label>
                        <input type="date" wire:model="due_date" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Visit date</label>
                        <input type="date" wire:model="visited_at" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Location</label>
                        <input type="text" wire:model="location" class="mt-1 w-full rounded-lg border-stone-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" wire:model="is_confidential" class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                    Confidential (pastoral only)
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800">
                        {{ $editingId ? 'Update entry' : 'Save entry' }}
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Cancel</button>
                    @endif
                </div>
            </form>
        </div>

        <div class="space-y-4">
            @forelse ($notes as $note)
                <article wire:key="care-{{ $note->id }}" class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">{{ ucfirst($note->type) }}</p>
                            <h3 class="mt-1 text-base font-bold text-stone-900">{{ $note->title ?: 'Untitled entry' }}</h3>
                            <p class="text-xs text-stone-500">
                                {{ $note->person?->first_name }} {{ $note->person?->last_name }}
                                @if ($note->visited_at) · Visited {{ $note->visited_at->format('M j, Y') }} @endif
                                @if ($note->location) · {{ $note->location }} @endif
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $note->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($note->status === 'archived' ? 'bg-stone-100 text-stone-600' : 'bg-amber-100 text-amber-800') }}">
                            {{ str_replace('_', ' ', ucfirst($note->status)) }}
                        </span>
                    </div>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-stone-700">{{ $note->body }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs">
                        <button wire:click="edit({{ $note->id }})" class="rounded-md border border-stone-300 px-3 py-1.5 font-medium text-stone-700 hover:bg-stone-50">Edit</button>
                        @if ($note->status !== 'completed')
                            <button wire:click="markComplete({{ $note->id }})" class="rounded-md border border-emerald-300 px-3 py-1.5 font-medium text-emerald-700 hover:bg-emerald-50">Mark complete</button>
                        @endif
                        <button wire:click="delete({{ $note->id }})" class="rounded-md border border-rose-300 px-3 py-1.5 font-medium text-rose-700 hover:bg-rose-50">Archive</button>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center text-sm text-stone-600">
                    No pastoral care entries yet. Start by adding a note for someone in your care.
                </div>
            @endforelse

            <div>{{ $notes->links() }}</div>
        </div>
    </section>
</div>
