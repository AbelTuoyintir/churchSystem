<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $contribution && $contribution->exists ? 'Edit Contribution' : 'Record Contribution' }}
        </h2>
        <a href="{{ route('contributions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
            Back to Contributions
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow p-6 space-y-6">
        <!-- Anonymous Toggle -->
        <div class="flex items-center">
            <input type="checkbox" id="is_anonymous" wire:model.live="is_anonymous" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="is_anonymous" class="ml-2 block text-sm font-medium text-gray-900">Anonymous Contribution</label>
        </div>

        <!-- Person Select (Hidden if Anonymous) -->
        @if (!$is_anonymous)
        <div>
            <label for="person_id" class="block text-sm font-medium text-gray-700">Contributor / Person</label>
            <div class="mb-2">
                <input type="text" wire:model.live.debounce.300ms="personSearch" placeholder="Search person by name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <select id="person_id" wire:model="person_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">-- Select Person (or leave blank) --</option>
                @foreach ($people as $person)
                    <option value="{{ $person->id }}">
                        {{ $person->last_name }}, {{ $person->first_name }} {{ $person->email ? "({$person->email})" : '' }}
                    </option>
                @endforeach
            </select>
            @error('person_id') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        @endif

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
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount ($) *</label>
                <input type="number" step="0.01" min="0.01" id="amount" wire:model="amount" placeholder="0.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('amount') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="contributed_on" class="block text-sm font-medium text-gray-700">Contributed On Date *</label>
                <input type="date" id="contributed_on" wire:model="contributed_on" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('contributed_on') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                <select id="method" wire:model="method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                    <option value="card">Card</option>
                    <option value="transfer">Transfer</option>
                    <option value="online">Online</option>
                </select>
                @error('method') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="reference" class="block text-sm font-medium text-gray-700">Reference / Check #</label>
            <input type="text" id="reference" wire:model="reference" placeholder="e.g. Check #1042 or Txn ID" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            @error('reference') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
            <textarea id="note" wire:model="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
            @error('note') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('contributions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium text-sm rounded-md hover:bg-indigo-700 shadow-sm">
                Save Contribution
            </button>
        </div>
    </form>
</div>
