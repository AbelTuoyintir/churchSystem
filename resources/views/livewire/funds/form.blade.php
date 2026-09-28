<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $fund && $fund->exists ? 'Edit Fund' : 'Create Fund' }}
        </h2>
        <a href="{{ route('funds.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
            Back to Funds
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Fund Name *</label>
            <input type="text" id="name" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            @error('name') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
            @error('description') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="goal_amount" class="block text-sm font-medium text-gray-700">Goal Amount ($)</label>
            <input type="number" step="0.01" min="0" id="goal_amount" wire:model="goal_amount" placeholder="e.g. 5000.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            @error('goal_amount') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center">
                <input type="checkbox" id="is_tax_deductible" wire:model="is_tax_deductible" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_tax_deductible" class="ml-2 block text-sm text-gray-900 font-medium">Tax Deductible</label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_active" wire:model="is_active" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-900 font-medium">Active</label>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('funds.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium text-sm rounded-md hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium text-sm rounded-md hover:bg-indigo-700 shadow-sm">
                Save Fund
            </button>
        </div>
    </form>
</div>
