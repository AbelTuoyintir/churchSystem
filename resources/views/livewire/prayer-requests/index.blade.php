<div class="mx-auto max-w-7xl space-y-6">
    @if (session()->has('success'))
        <div class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <header class="border-b border-gray-200 pb-5">
        <p class="text-sm font-semibold uppercase text-emerald-700">Community</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Prayer Requests</h1>
        <p class="mt-1 text-sm text-gray-600">Share a request and pray with the church.</p>
    </header>

    <section class="rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-bold text-gray-900">Submit a request</h2>
        <form wire:submit="submit" class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Title (optional)</label>
                <input type="text" wire:model="title" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Request</label>
                <textarea wire:model="body" rows="4" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600"></textarea>
                @error('body') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Visibility</label>
                <select wire:model.live="scope" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                    <option value="public">Public prayer wall</option>
                    <option value="group">My group</option>
                    <option value="pastoral">Pastoral only</option>
                </select>
            </div>
            @if ($scope === 'group')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Group</label>
                    <select wire:model="group_id" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                        <option value="">Select a group</option>
                        @foreach ($myGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            @endif
            <button type="submit" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Submit request</button>
        </form>
    </section>

    <section>
        <h2 class="border-b border-gray-200 pb-3 text-xl font-bold text-gray-900">Public prayer wall</h2>
        @forelse ($publicWall as $request)
            <article wire:key="prayer-{{ $request->id }}" class="border-b border-gray-200 py-4">
                <div class="flex items-center gap-3">
                    @if ($request->person?->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($request->person->photo_path) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">
                            {{ strtoupper(substr($request->person->first_name ?? '', 0, 1).substr($request->person->last_name ?? '', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $request->person?->full_name ?? 'Anonymous' }}</p>
                        <p class="text-xs text-gray-500">{{ $request->created_at?->format('M j, Y') }}</p>
                    </div>
                </div>
                @if ($request->title)
                    <h3 class="mt-2 font-semibold text-gray-900">{{ $request->title }}</h3>
                @endif
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $request->body }}</p>
                <p class="mt-2 text-xs uppercase text-gray-500">{{ $request->status }}</p>
            </article>
        @empty
            <p class="py-4 text-sm text-gray-600">No public prayer requests yet.</p>
        @endforelse
        <div>{{ $publicWall->links() }}</div>
    </section>

    <section>
        <h2 class="border-b border-gray-200 pb-3 text-xl font-bold text-gray-900">My requests</h2>
        @forelse ($myRequests as $request)
            <article wire:key="my-prayer-{{ $request->id }}" class="border-b border-gray-200 py-4">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-gray-900">{{ $request->title ?: 'Prayer request' }}</p>
                    <span class="text-xs uppercase text-gray-500">{{ $request->scope }} · {{ $request->status }}</span>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $request->body }}</p>
                @if ($request->group)
                    <p class="mt-1 text-xs text-gray-500">Group: {{ $request->group->name }}</p>
                @endif
            </article>
        @empty
            <p class="py-4 text-sm text-gray-600">You have not submitted any prayer requests.</p>
        @endforelse
    </section>
</div>
