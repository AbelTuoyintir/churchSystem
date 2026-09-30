<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Message Details
            </h2>
            <div class="space-x-2">
                @can('update', $message)
                    @if($message->status === 'draft')
                        <a href="{{ route('messages.edit', $message) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Edit Message
                        </a>
                    @endif
                @endcan
                <a href="{{ route('messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Back to Messages
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Message Details Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-4 border-b border-gray-200 mb-4">
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase">Channel</span>
                <span class="text-sm font-semibold text-gray-900 capitalize">{{ $message->channel }}</span>
            </div>
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase">Status</span>
                <span class="text-sm font-semibold capitalize">
                    @if($message->status === 'sent')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sent</span>
                    @elseif($message->status === 'sending')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sending</span>
                    @elseif($message->status === 'scheduled')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Scheduled</span>
                    @elseif($message->status === 'failed')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase">Scheduled At</span>
                <span class="text-sm text-gray-900">{{ $message->scheduled_at ? $message->scheduled_at->format('M j, Y g:i A') : '—' }}</span>
            </div>
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase">Sent At</span>
                <span class="text-sm text-gray-900">{{ $message->sent_at ? $message->sent_at->format('M j, Y g:i A') : '—' }}</span>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $message->subject ?: '(No Subject)' }}</h3>
            <div class="p-4 bg-gray-50 rounded-md border border-gray-200 whitespace-pre-wrap text-sm text-gray-800">
                {{ $message->body }}
            </div>
        </div>
    </div>

    <!-- Recipients Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipients</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Channel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recipients as $recipient)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($recipient->person)
                                    <a href="{{ route('people.edit', $recipient->person) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $recipient->person->first_name }} {{ $recipient->person->last_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                {{ $recipient->channel }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $recipient->address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($recipient->status === 'sent' || $recipient->status === 'delivered')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ ucfirst($recipient->status) }}</span>
                                @elseif($recipient->status === 'failed' || $recipient->status === 'bounced')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($recipient->status) }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($recipient->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $recipient->sent_at ? $recipient->sent_at->format('M j, Y g:i A') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-red-600">
                                {{ $recipient->error ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No recipients recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $recipients->links() }}
        </div>
    </div>
</div>
