<div>
    <div class="space-y-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Church profile and messaging</h2>
                <p class="mt-1 text-sm text-gray-500">Provider secrets are write-only. Leave a secret blank to keep its stored value.</p>
            </div>

            <form wire:submit="saveSettings" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="church-name" class="mb-1 block text-sm font-medium text-gray-700">Church name</label>
                    <input id="church-name" wire:model="churchName" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('churchName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="currency" class="mb-1 block text-sm font-medium text-gray-700">Currency code</label>
                    <input id="currency" wire:model="currency" maxlength="3" class="w-full rounded-md border-gray-300 text-sm uppercase shadow-sm">
                    @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="church-address" class="mb-1 block text-sm font-medium text-gray-700">Church address</label>
                    <textarea id="church-address" wire:model="churchAddress" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('churchAddress') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message-provider" class="mb-1 block text-sm font-medium text-gray-700">Message provider</label>
                    <select id="message-provider" wire:model="messageProvider" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="none">Not configured</option>
                        <option value="smtp">SMTP</option>
                        <option value="twilio">Twilio</option>
                        <option value="custom">Custom provider</option>
                    </select>
                    @error('messageProvider') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp-host" class="mb-1 block text-sm font-medium text-gray-700">SMTP host</label>
                    <input id="smtp-host" wire:model="smtpHost" autocomplete="off" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('smtpHost') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp-port" class="mb-1 block text-sm font-medium text-gray-700">SMTP port</label>
                    <input id="smtp-port" type="number" wire:model="smtpPort" min="1" max="65535" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('smtpPort') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp-username" class="mb-1 block text-sm font-medium text-gray-700">SMTP username</label>
                    <input id="smtp-username" wire:model="smtpUsername" autocomplete="off" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('smtpUsername') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp-password" class="mb-1 block text-sm font-medium text-gray-700">SMTP password</label>
                    <input id="smtp-password" type="password" wire:model="smtpPassword" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('smtpPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="sms-api-key" class="mb-1 block text-sm font-medium text-gray-700">SMS provider API key</label>
                    <input id="sms-api-key" type="password" wire:model="smsApiKey" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('smsApiKey') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end justify-end md:col-span-2">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:opacity-60">Save settings</button>
                </div>
            </form>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold text-gray-900">Service times</h2>
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">Manage services</a>
                </div>
                @forelse ($services as $service)
                    <div wire:key="settings-service-{{ $service->id }}" class="flex items-center justify-between gap-3 border-t border-gray-100 py-3 text-sm">
                        <div>
                            <p class="font-medium text-gray-800">{{ $service->name }}</p>
                            <p class="text-gray-500">{{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$service->day_of_week] ?? 'Day not set' }} · {{ $service->start_time }} · {{ $service->location }}</p>
                        </div>
                        <span class="text-xs {{ $service->is_active ? 'text-emerald-700' : 'text-gray-500' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                @empty
                    <p class="border-t border-gray-100 py-3 text-sm text-gray-500">No services configured.</p>
                @endforelse
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold text-gray-900">Fund configuration</h2>
                    <a href="{{ route('funds.index') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">Manage funds</a>
                </div>
                @forelse ($funds as $fund)
                    <div wire:key="settings-fund-{{ $fund->id }}" class="flex items-center justify-between gap-3 border-t border-gray-100 py-3 text-sm">
                        <div>
                            <p class="font-medium text-gray-800">{{ $fund->name }}</p>
                            <p class="text-gray-500">{{ $fund->goal_amount ? number_format((float) $fund->goal_amount, 2).' '.$currency : 'No goal set' }}</p>
                        </div>
                        <span class="text-xs {{ $fund->is_active ? 'text-emerald-700' : 'text-gray-500' }}">{{ $fund->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                @empty
                    <p class="border-t border-gray-100 py-3 text-sm text-gray-500">No funds configured.</p>
                @endforelse
            </section>
        </div>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Communication templates</h2>
                <p class="mt-1 text-sm text-gray-500">Create, edit, and remove reusable message content.</p>
            </div>

            <form wire:submit="saveTemplate" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="template-name" class="mb-1 block text-sm font-medium text-gray-700">Template name</label>
                    <input id="template-name" wire:model="templateName" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('templateName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="template-channel" class="mb-1 block text-sm font-medium text-gray-700">Channel</label>
                    <select id="template-channel" wire:model="templateChannel" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="both">Email and SMS</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="template-subject" class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                    <input id="template-subject" wire:model="templateSubject" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="template-body" class="mb-1 block text-sm font-medium text-gray-700">Message body</label>
                    <textarea id="template-body" wire:model="templateBody" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('templateBody') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-2 md:col-span-2">
                    <button type="submit" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">{{ $editingTemplateId ? 'Update template' : 'Add template' }}</button>
                    @if ($editingTemplateId)
                        <button type="button" wire:click="resetTemplateForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel edit</button>
                    @endif
                </div>
            </form>

            <div class="mt-6 overflow-x-auto border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead><tr class="text-left text-xs font-semibold uppercase text-gray-500"><th class="py-3 pr-4">Name</th><th class="py-3 pr-4">Channel</th><th class="py-3 pr-4">Subject</th><th class="py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($templates as $template)
                            <tr wire:key="template-{{ $template->id }}">
                                <td class="py-3 pr-4 font-medium text-gray-800">{{ $template->name }}</td>
                                <td class="py-3 pr-4 capitalize text-gray-600">{{ $template->channel }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $template->subject }}</td>
                                <td class="py-3 text-right whitespace-nowrap">
                                    <button type="button" wire:click="editTemplate({{ $template->id }})" class="font-medium text-indigo-700 hover:text-indigo-900">Edit</button>
                                    <button type="button" wire:click="deleteTemplate({{ $template->id }})" wire:confirm="Delete this communication template?" class="ml-3 font-medium text-red-700 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-5 text-center text-gray-500">No templates configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Role-permission matrix</h2>
                <p class="mt-1 text-sm text-gray-500">Choose a role, then save its access to each church-wide module.</p>
            </div>
            <div class="mb-5 max-w-sm">
                <label for="permission-role" class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                <select id="permission-role" wire:model.live="selectedRoleId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <form wire:submit="saveRolePermissions">
                <div class="space-y-3">
                    @foreach ($modules as $module => $definition)
                        <fieldset wire:key="permission-module-{{ $module }}" class="border-t border-gray-100 pt-3">
                            <legend class="mb-2 text-sm font-semibold text-gray-800">{{ $definition['label'] }}</legend>
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                @foreach ($actions as $action)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $module }}.{{ $action }}" @disabled($roles->firstWhere('id', $selectedRoleId)?->name === 'admin') class="rounded border-gray-300 text-indigo-700 focus:ring-indigo-600 disabled:opacity-60">
                                        <span class="capitalize">{{ $action }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>
                <button type="submit" class="mt-5 rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Save permissions</button>
            </form>
        </section>
    </div>
</div>
