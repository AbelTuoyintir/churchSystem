<div>
    <section class="max-w-4xl rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Export builder</h2>
            <p class="mt-1 text-sm text-gray-500">Every export is recorded in the audit log. Directory exports include only people who opted in.</p>
        </div>

        <form wire:submit="export" class="space-y-5">
            <div>
                <label for="export-dataset" class="mb-1 block text-sm font-medium text-gray-700">Dataset</label>
                <select id="export-dataset" wire:model.live="dataset" class="w-full max-w-md rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="giving_statements">Giving statements</option>
                    <option value="directory">Directory</option>
                    <option value="attendance">Attendance</option>
                    <option value="custom_report">Custom report</option>
                </select>
                @error('dataset') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($dataset === 'custom_report')
                <div>
                    <label for="custom-dataset" class="mb-1 block text-sm font-medium text-gray-700">Report data</label>
                    <select id="custom-dataset" wire:model="customDataset" class="w-full max-w-md rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="giving_statements">Giving statements</option>
                        <option value="directory">Directory</option>
                        <option value="attendance">Attendance</option>
                    </select>
                </div>
            @endif

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-700">Date range <span class="font-normal text-gray-500">(optional)</span></legend>
                <div class="grid max-w-md gap-3 sm:grid-cols-2">
                    <div>
                        <label for="export-from" class="mb-1 block text-xs text-gray-500">From</label>
                        <input id="export-from" type="date" wire:model="fromDate" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('fromDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="export-through" class="mb-1 block text-xs text-gray-500">Through</label>
                        <input id="export-through" type="date" wire:model="throughDate" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('throughDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:cursor-wait disabled:opacity-60">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                <span wire:loading.remove>Download CSV</span>
                <span wire:loading>Preparing export...</span>
            </button>
        </form>
    </section>
</div>
