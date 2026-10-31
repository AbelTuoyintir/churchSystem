<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div><p class="text-sm font-semibold uppercase text-emerald-700">Leadership</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Group Prayer Requests</h1></div>
        @if ($groups->isNotEmpty())
            <label class="text-sm font-medium text-gray-700">Group
                <select wire:model.live="groupId" class="mt-1 block min-w-48 rounded-md border-gray-300 text-sm">
                    @foreach ($groups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach
                </select>
            </label>
        @endif
    </header>
    @if (session()->has('success'))<p class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</p>@endif
    @if ($groups->isNotEmpty())
        <section class="grid gap-8 lg:grid-cols-[minmax(18rem,0.8fr)_minmax(0,1.2fr)]">
            <form wire:submit="save" class="space-y-4 border-b border-gray-200 pb-6 lg:border-b-0 lg:border-r lg:pr-8">
                <h2 class="text-base font-bold text-gray-900">Add a group request</h2>
                <label class="block text-sm font-medium text-gray-700">Title (optional)<input wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
                <label class="block text-sm font-medium text-gray-700">Request<textarea wire:model="body" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></textarea></label>
                @error('body')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                <button class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Save request</button>
            </form>
            <section><h2 class="text-base font-bold text-gray-900">Requests for your groups</h2><div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
                @forelse ($requests as $request)
                    <article class="py-4" wire:key="prayer-request-{{ $request->id }}">
                        <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="font-semibold text-gray-900">{{ $request->title ?: 'Prayer request' }}</p><p class="mt-1 text-sm text-gray-700">{{ $request->body }}</p><p class="mt-2 text-xs text-gray-500">{{ $request->group->name }} · {{ $request->person?->full_name ?? 'Anonymous' }} · {{ $request->created_at->format('M j, Y') }}</p></div>
                            <select aria-label="Prayer request status" wire:change="updateStatus({{ $request->id }}, $event.target.value)" class="rounded-md border-gray-300 text-xs"><option value="open" @selected($request->status === 'open')>Open</option><option value="praying" @selected($request->status === 'praying')>Praying</option><option value="answered" @selected($request->status === 'answered')>Answered</option></select>
                        </div>
                    </article>
                @empty<p class="py-6 text-sm text-gray-600">No group prayer requests yet.</p>@endforelse
            </div><div class="mt-4">{{ $requests->links() }}</div></section>
        </section>
    @else
        <p class="py-10 text-sm text-gray-600">No groups are assigned to your leadership yet.</p>
    @endif
</div>