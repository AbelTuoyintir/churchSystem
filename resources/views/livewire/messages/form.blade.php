<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $message && $message->exists ? 'Edit Message' : 'Create Message' }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Channel & Scheduled At -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="channel" class="block text-sm font-medium text-gray-700">Channel <span class="text-red-500">*</span></label>
                    <select id="channel" wire:model.live="channel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                    </select>
                    @error('channel') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule Send (Optional)</label>
                    <input type="datetime-local" id="scheduled_at" wire:model="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('scheduled_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" id="subject" wire:model="subject" placeholder="Enter message subject..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('subject') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Body -->
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700">Body <span class="text-red-500">*</span></label>
                <textarea id="body" wire:model="body" rows="6" placeholder="Write your message content here..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                @error('body') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Audience Builder Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Audience Builder</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div>
                        <label for="membership_status" class="block text-sm font-medium text-gray-700">Membership Status</label>
                        <select id="membership_status" wire:model.live="membership_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Membership Statuses</option>
                            <option value="member">Member</option>
                            <option value="attender">Attender</option>
                            <option value="visitor">Visitor</option>
                        </select>
                        @error('membership_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="group_id" class="block text-sm font-medium text-gray-700">Group</label>
                        <select id="group_id" wire:model.live="group_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Groups</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('group_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="team_id" class="block text-sm font-medium text-gray-700">Team</label>
                        <select id="team_id" wire:model.live="team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Teams</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('team_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center space-x-6 mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="email_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Must be opted-in to Email</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="sms_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Must be opted-in to SMS</span>
                    </label>
                </div>

                <!-- Live Matching Counter Box -->
                <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-md flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="text-sm font-medium text-indigo-900">Matching Audience</span>
                    </div>
                    <div class="text-lg font-bold text-indigo-700" id="live-matching-count">
                        {{ $matchingCount }} {{ Str::plural('person', $matchingCount) }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('messages.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 shadow-sm">
                    Save Message
                </button>
            </div>
        </form>
    </div>
</div>
