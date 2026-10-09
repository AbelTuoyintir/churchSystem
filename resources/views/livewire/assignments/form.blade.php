<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $assignment && $assignment->exists ? 'Edit Serving Assignment' : 'Create Serving Assignment' }}
        </h2>
        <a href="{{ route('assignments.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">← Back to Assignments</a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Serving Team *</label>
                <select wire:model.live="team_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">-- Choose Team --</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('team_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Position</label>
                <select wire:model="position_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- General / Any --</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                    @endforeach
                </select>
                @error('position_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Person *</label>
                <select wire:model="person_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">-- Choose Person --</option>
                    @foreach($people as $person)
                        <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                    @endforeach
                </select>
                @error('person_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Date *</label>
                <input type="date" wire:model="assigned_on" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                @error('assigned_on') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Service</label>
                <select wire:model="service_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- None / N/A --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->starts_at?->format('M d, Y') }})</option>
                    @endforeach
                </select>
                @error('service_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Event</label>
                <select wire:model="event_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- None / N/A --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->starts_at?->format('M d, Y') }})</option>
                    @endforeach
                </select>
                @error('event_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                <select wire:model="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="assigned">Assigned</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="declined">Declined</option>
                </select>
                @error('status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                <textarea wire:model="notes" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('notes') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('assignments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Save Assignment</button>
        </div>
    </form>
</div>
