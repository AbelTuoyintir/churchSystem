<?php

namespace App\Livewire\Admin\AuditLog;

use App\Models\AuditLog as AuditLogEntry;
use App\Services\AuditLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $action = '';

    public string $fromDate = '';

    public string $throughDate = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function export(AuditLogger $auditLogger)
    {
        $this->authorizeAdmin();

        $entries = $this->entriesQuery()->with('actor')->orderBy('id');
        $entryCount = (clone $entries)->count();
        $auditLogger->record('export', 'audit_logs', null, 'Exported the audit log', ['entries' => $entryCount]);

        return response()->streamDownload(function () use ($entries): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['When', 'Who', 'Action', 'Subject', 'Record', 'What', 'IP address', 'User agent']);

            $entries->chunkById(500, function ($batch) use ($output): void {
                foreach ($batch as $entry) {
                    fputcsv($output, array_map($this->csvCell(...), [
                        $entry->created_at?->toIso8601String(),
                        $entry->actor?->email ?? "User {$entry->actor_id}",
                        $entry->action,
                        $entry->subject_type,
                        $entry->subject_id,
                        $entry->description,
                        $entry->ip_address,
                        $entry->user_agent,
                    ]));
                }
            });

            fclose($output);
        }, 'audit-log-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $this->authorizeAdmin();

        return view('livewire.admin.audit-log.index', [
            'entries' => $this->entriesQuery()->with('actor')->latest()->paginate(25),
            'actions' => AuditLogEntry::query()->distinct()->orderBy('action')->pluck('action'),
        ])->layout('layouts.app', ['header' => 'Audit Log']);
    }

    private function entriesQuery()
    {
        return AuditLogEntry::query()
            ->when($this->action !== '', fn ($query) => $query->where('action', $this->action))
            ->when($this->fromDate !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->throughDate !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->throughDate))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($nestedQuery): void {
                    $nestedQuery->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('subject_type', 'like', '%'.$this->search.'%')
                        ->orWhere('subject_id', 'like', '%'.$this->search.'%')
                        ->orWhere('ip_address', 'like', '%'.$this->search.'%')
                        ->orWhereHas('actor', fn ($actorQuery) => $actorQuery
                            ->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%'));
                });
            });
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function csvCell(mixed $value): mixed
    {
        if (is_string($value) && preg_match('/^[=+@\\-\t\r]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
