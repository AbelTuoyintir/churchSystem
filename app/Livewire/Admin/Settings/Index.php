<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ChurchSetting;
use App\Models\CommunicationTemplate;
use App\Models\Fund;
use App\Models\Service;
use App\Services\AuditLogger;
use App\Services\RolePermissionMatrix;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class Index extends Component
{
    public string $churchName = '';

    public string $churchAddress = '';

    public string $currency = 'GHS';

    public string $messageProvider = 'none';

    public string $smtpHost = '';

    public string $smtpPort = '';

    public string $smtpUsername = '';

    public string $smtpPassword = '';

    public string $smsApiKey = '';

    public ?int $editingTemplateId = null;

    public string $templateName = '';

    public string $templateChannel = 'email';

    public string $templateSubject = '';

    public string $templateBody = '';

    public ?int $selectedRoleId = null;

    public array $selectedPermissions = [];

    public function mount(): void
    {
        $this->authorizeAdmin();
        app(RolePermissionMatrix::class)->initializeDefaults();

        $this->churchName = $this->setting('church_name', config('app.name', 'Church Management System'));
        $this->churchAddress = $this->setting('church_address');
        $this->currency = $this->setting('currency', 'GHS');
        $this->messageProvider = $this->setting('message_provider', 'none');
        $this->smtpHost = $this->setting('smtp_host');
        $this->smtpPort = $this->setting('smtp_port');
        $this->smtpUsername = $this->setting('smtp_username');
        $this->selectedRoleId = Role::query()->where('name', 'admin')->value('id');
        $this->loadRolePermissions($this->selectedRoleId);
    }

    public function saveSettings(AuditLogger $auditLogger): void
    {
        $this->authorizeAdmin();

        $validated = $this->validate([
            'churchName' => ['required', 'string', 'max:255'],
            'churchAddress' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', 'string', 'size:3'],
            'messageProvider' => ['required', Rule::in(['none', 'smtp', 'twilio', 'custom'])],
            'smtpHost' => ['nullable', 'string', 'max:255'],
            'smtpPort' => ['nullable', 'integer', 'between:1,65535'],
            'smtpUsername' => ['nullable', 'string', 'max:255'],
            'smtpPassword' => ['nullable', 'string', 'max:4096'],
            'smsApiKey' => ['nullable', 'string', 'max:4096'],
        ]);

        foreach ([
            'church_name' => $validated['churchName'],
            'church_address' => $validated['churchAddress'] ?? '',
            'currency' => strtoupper($validated['currency']),
            'message_provider' => $validated['messageProvider'],
            'smtp_host' => $validated['smtpHost'] ?? '',
            'smtp_port' => (string) ($validated['smtpPort'] ?? ''),
            'smtp_username' => $validated['smtpUsername'] ?? '',
        ] as $key => $value) {
            ChurchSetting::query()->updateOrCreate(['key' => $key], [
                'value' => $value,
                'is_secret' => false,
            ]);
        }

        foreach ([
            'smtp_password' => $validated['smtpPassword'] ?? '',
            'sms_api_key' => $validated['smsApiKey'] ?? '',
        ] as $key => $secret) {
            if ($secret !== '') {
                ChurchSetting::query()->updateOrCreate(['key' => $key], [
                    'value' => $secret,
                    'is_secret' => true,
                ]);
            }
        }

        $auditLogger->record('update', 'church_settings', null, 'Updated church settings', [
            'fields' => ['church_name', 'church_address', 'currency', 'message_provider', 'smtp_host', 'smtp_port', 'smtp_username'],
        ]);

        $this->smtpPassword = '';
        $this->smsApiKey = '';
        $this->dispatch('notify', type: 'success', message: 'Church settings saved.');
    }

    public function saveTemplate(AuditLogger $auditLogger): void
    {
        $this->authorizeAdmin();

        $validated = $this->validate([
            'templateName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('communication_templates', 'name')->ignore($this->editingTemplateId),
            ],
            'templateChannel' => ['required', Rule::in(['email', 'sms', 'both'])],
            'templateSubject' => ['nullable', 'string', 'max:255'],
            'templateBody' => ['required', 'string', 'max:10000'],
        ]);

        $template = $this->editingTemplateId
            ? CommunicationTemplate::query()->findOrFail($this->editingTemplateId)
            : new CommunicationTemplate;
        $template->fill([
            'name' => $validated['templateName'],
            'channel' => $validated['templateChannel'],
            'subject' => $validated['templateSubject'] ?? null,
            'body' => $validated['templateBody'],
        ])->save();

        $auditLogger->record($this->editingTemplateId ? 'update' : 'create', 'communication_templates', (string) $template->id, 'Saved a communication template');
        $this->resetTemplateForm();
        $this->dispatch('notify', type: 'success', message: 'Communication template saved.');
    }

    public function editTemplate(int $templateId): void
    {
        $this->authorizeAdmin();
        $template = CommunicationTemplate::query()->findOrFail($templateId);
        $this->editingTemplateId = $template->id;
        $this->templateName = $template->name;
        $this->templateChannel = $template->channel;
        $this->templateSubject = $template->subject ?? '';
        $this->templateBody = $template->body;
    }

    public function deleteTemplate(int $templateId, AuditLogger $auditLogger): void
    {
        $this->authorizeAdmin();
        $template = CommunicationTemplate::query()->findOrFail($templateId);
        $auditLogger->record('delete', 'communication_templates', (string) $template->id, 'Deleted a communication template');
        $template->delete();
        $this->dispatch('notify', type: 'success', message: 'Communication template deleted.');
    }

    public function loadRolePermissions(?int $roleId): void
    {
        $this->authorizeAdmin();
        $this->selectedRoleId = $roleId;
        $role = $roleId ? Role::query()->findOrFail($roleId) : null;
        $this->selectedPermissions = $role?->permissions()
            ->whereIn('name', RolePermissionMatrix::permissionNames())
            ->pluck('name')
            ->all() ?? [];
    }

    public function updatedSelectedRoleId(?int $roleId): void
    {
        $this->loadRolePermissions($roleId);
    }

    public function saveRolePermissions(AuditLogger $auditLogger): void
    {
        $this->authorizeAdmin();
        $role = Role::query()->findOrFail($this->selectedRoleId);
        abort_unless($role->name !== 'admin', 403);

        $validated = $this->validate([
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', Rule::in(RolePermissionMatrix::permissionNames())],
        ]);

        $managedNames = RolePermissionMatrix::permissionNames();
        $preservedPermissions = $role->permissions()->whereNotIn('name', $managedNames)->get();
        $selectedPermissions = Permission::query()->whereIn('name', $validated['selectedPermissions'] ?? [])->get();
        $role->syncPermissions($preservedPermissions->merge($selectedPermissions));

        $auditLogger->record('update', 'roles', (string) $role->id, 'Updated role permissions', [
            'role' => $role->name,
            'permissions' => $validated['selectedPermissions'] ?? [],
        ]);

        $this->dispatch('notify', type: 'success', message: 'Role permissions saved.');
    }

    public function render()
    {
        $this->authorizeAdmin();

        $services = Service::query()->orderBy('day_of_week')->orderBy('start_time')->get();
        $funds = Fund::query()->orderBy('name')->get();

        return view('livewire.admin.settings.index', [
            'services' => $services,
            'funds' => $funds,
            'templates' => CommunicationTemplate::query()->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
            'modules' => RolePermissionMatrix::MODULES,
            'actions' => RolePermissionMatrix::ACTIONS,
        ])->layout('layouts.app', ['header' => 'Settings']);
    }

    private function setting(string $key, string $default = ''): string
    {
        return (string) (ChurchSetting::query()->where('key', $key)->first()?->value ?? $default);
    }

    public function resetTemplateForm(): void
    {
        $this->authorizeAdmin();
        $this->reset(['editingTemplateId', 'templateName', 'templateSubject', 'templateBody']);
        $this->templateChannel = 'email';
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
