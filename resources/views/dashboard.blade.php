<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <livewire:dashboard.admin-overview />

        <div class="mt-6">
            <livewire:dashboard.attendance-this-week />
        </div>
    </div>
</x-app-layout>
