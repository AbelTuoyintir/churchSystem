<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $pledge && $pledge->exists ? 'Edit Pledge' : 'Create Pledge' }}
        </h2>
        <a href="{{ route('pledges.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
            Back to Pledges
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
            <label for="person_id" class="block text-sm font-medium text-gray-700">Person *</label>
            <div class="mb-2">
                <input type="text" wire:model.live.debounce.300ms="personSearch" placeholder="Search person by name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <select id="person_id" wire:model="person_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">-- Select Person --</option>
                @foreach ($people as $person)
                    <option value="{{ $person->id }}">
                        {{ $person->last_name }}, {{ $person->first_name }} {{ $person->email ? "({$person->email})" : '' }}
                    </option>
                @endforeach
            </select>
            @error('person_id') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="fund_id" class="block text-sm font-medium text-gray-700">Fund *</label>
                <select id="fund_id" wire:model="fund_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select Fund --</option>
                    @foreach ($funds as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
                @error('fund_id') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Pledge Amount ($) *</label>
                <input type="number" step="0.01" min="0.01" id="amount" wire:model="amount" placeholder="0.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('amount') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency *</label>
                <select id="frequency" wire:model="frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="one_time">One Time</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="annual">Annual</option>
                </select>
                @error('frequency') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                <input type="date" id="start_date" wire:model="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('start_date') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" id="end_date" wire:model="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('end_date') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('pledges.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium text-sm rounded-md hover:bg-indigo-700 shadow-sm">
                Save Pledge
            </button>
        </div>
    </form>
</div>
