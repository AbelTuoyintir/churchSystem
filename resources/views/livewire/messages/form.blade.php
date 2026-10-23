<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <div class="border-b border-gray-200 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $message && $message->exists ? 'Edit Broadcast Message' : 'Create Broadcast Message' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Send updates, reminders, and alerts to church members via SMS, Email, or both.
                </p>
            </div>
            <a href="{{ route('messages.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900">
                &larr; Back to Broadcasts
            </a>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Channel Selector -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Delivery Channel <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition {{ $channel === 'email' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" wire:model.live="channel" value="email" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <span class="p-2 rounded-md bg-indigo-100 text-indigo-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Email</span>
                                <span class="block text-xs text-gray-500">Full HTML email</span>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition {{ $channel === 'sms' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" wire:model.live="channel" value="sms" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <span class="p-2 rounded-md bg-green-100 text-green-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">SMS</span>
                                <span class="block text-xs text-gray-500">Direct text message</span>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition {{ $channel === 'both' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" wire:model.live="channel" value="both" class="sr-only">
                        <div class="flex items-center space-x-3">
                            <span class="p-2 rounded-md bg-purple-100 text-purple-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Both</span>
                                <span class="block text-xs text-gray-500">Email &amp; SMS</span>
                            </div>
                        </div>
                    </label>
                </div>
                @error('channel') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Target Audience Selector -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Target Audience <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Entire Church Option -->
                    <label class="relative flex p-4 border rounded-lg cursor-pointer transition {{ $target_audience === 'church' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500' : 'border-gray-200 hover:bg-gray-50' }} {{ !$canBroadcastToChurch ? 'opacity-50 cursor-not-allowed bg-gray-50' : '' }}">
                        <input type="radio" wire:model.live="target_audience" value="church" {{ !$canBroadcastToChurch ? 'disabled' : '' }} class="sr-only">
                        <div class="flex items-start space-x-3">
                            <span class="p-2 rounded-md bg-blue-100 text-blue-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4"/></svg>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Entire Church</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Broadcast to all church members &amp; attenders</span>
                                @if(!$canBroadcastToChurch)
                                    <span class="block text-xs text-amber-700 mt-1 font-medium">Reserved for Pastors &amp; Admins</span>
                                @endif
                            </div>
                        </div>
                    </label>

                    <!-- Specific Group Option -->
                    <label class="relative flex p-4 border rounded-lg cursor-pointer transition {{ $target_audience === 'group' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" wire:model.live="target_audience" value="group" class="sr-only">
                        <div class="flex items-start space-x-3">
                            <span class="p-2 rounded-md bg-amber-100 text-amber-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Specific Group</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Target a ministry, department, or small group</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Group Dropdown (when target_audience is 'group') -->
            @if($target_audience === 'group')
                <div class="bg-amber-50/50 p-4 rounded-lg border border-amber-200">
                    <label for="group_id" class="block text-sm font-semibold text-gray-800 mb-1">Select Group <span class="text-red-500">*</span></label>
                    <select id="group_id" wire:model.live="group_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Choose a Group --</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }} ({{ ucfirst($grp->type) }})</option>
                        @endforeach
                    </select>
                    @error('group_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-semibold text-gray-800 mb-1">Subject / Headline</label>
                <input type="text" id="subject" wire:model="subject" placeholder="e.g. Sunday Service Reminder, Prayer Meeting Update..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('subject') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Body -->
            <div>
                <label for="body" class="block text-sm font-semibold text-gray-800 mb-1">Message Content <span class="text-red-500">*</span></label>
                <textarea id="body" wire:model="body" rows="6" placeholder="Write your announcement or pastoral message here..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                <div class="flex justify-between items-center text-xs text-gray-500 mt-1">
                    <span>Line breaks will be preserved in email and SMS</span>
                    @if($channel === 'sms' || $channel === 'both')
                        <span>SMS segments: approx. 160 characters per SMS</span>
                    @endif
                </div>
                @error('body') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Schedule Send -->
            <div>
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Schedule for later (Optional)</label>
                <input type="datetime-local" id="scheduled_at" wire:model="scheduled_at" class="block w-full sm:w-72 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <span class="text-xs text-gray-500 mt-1 block">Leave empty to send immediately or save as draft.</span>
                @error('scheduled_at') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Matching Audience Live Counter Box -->
            <div class="p-4 bg-indigo-50/80 border border-indigo-200 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <span class="p-2 rounded-full bg-indigo-100 text-indigo-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-indigo-800">Target Audience Match</div>
                        <div class="text-lg font-bold text-indigo-950">
                            {{ $matchingCount }} recipient{{ $matchingCount === 1 ? '' : 's' }}
                        </div>
                    </div>
                </div>
                <div class="text-xs text-indigo-700 max-w-sm">
                    Recipients resolved based on channel availability (verified email / mobile phone number).
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 pt-5 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3">
                <a href="{{ route('messages.index') }}" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center">
                    Cancel
                </a>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save as Draft
                    </button>

                    <button type="button" wire:click="sendNow" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span wire:loading.remove wire:target="sendNow">Send Broadcast Now</span>
                        <span wire:loading wire:target="sendNow">Sending Broadcast...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
