<?php

namespace App\Livewire\Admin\Exports;

use App\Models\Attendance;
use App\Models\Contribution;
use App\Models\Person;
use App\Services\AuditLogger;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    public string $dataset = 'giving_statements';

    public string $customDataset = 'giving_statements';

    public string $fromDate = '';

    public string $throughDate = '';

    public function export(AuditLogger $auditLogger): StreamedResponse
    {
        $this->authorizeAdmin();

        $validated = $this->validate([
            'dataset' => ['required', 'in:giving_statements,directory,attendance,custom_report'],
            'customDataset' => ['required', 'in:giving_statements,directory,attendance'],
            'fromDate' => ['nullable', 'date'],
            'throughDate' => ['nullable', 'date', 'after_or_equal:fromDate'],
        ]);

        $selectedDataset = $validated['dataset'] === 'custom_report'
            ? $validated['customDataset']
            : $validated['dataset'];

        $auditLogger->record('export', $selectedDataset, null, 'Ran admin export', [
            'dataset' => $validated['dataset'],
            'resolved_dataset' => $selectedDataset,
            'from' => $validated['fromDate'] ?: null,
            'through' => $validated['throughDate'] ?: null,
        ]);

        $filename = $selectedDataset.'-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($selectedDataset): void {
            $output = fopen('php://output', 'wb');

            match ($selectedDataset) {
                'giving_statements' => $this->writeGivingStatements($output),
                'directory' => $this->writeDirectory($output),
                'attendance' => $this->writeAttendance($output),
            };

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $this->authorizeAdmin();

        return view('livewire.admin.exports.index')->layout('layouts.app', ['header' => 'Exports']);
    }

    private function writeGivingStatements($output): void
    {
        fputcsv($output, ['Member', 'Email', 'Fund', 'Contribution date', 'Amount', 'Method', 'Reference']);

        Contribution::query()
            ->with(['person', 'fund'])
            ->when($this->fromDate !== '', fn ($query) => $query->whereDate('contributed_on', '>=', $this->fromDate))
            ->when($this->throughDate !== '', fn ($query) => $query->whereDate('contributed_on', '<=', $this->throughDate))
            ->orderBy('id')
            ->chunkById(500, function ($contributions) use ($output): void {
                foreach ($contributions as $contribution) {
                    fputcsv($output, array_map($this->csvCell(...), [
                        $contribution->person?->full_name,
                        $contribution->person?->email,
                        $contribution->fund?->name,
                        $contribution->contributed_on?->toDateString(),
                        $contribution->amount,
                        $contribution->method,
                        $contribution->reference,
                    ]));
                }
            });
    }

    private function writeDirectory($output): void
    {
        fputcsv($output, ['First name', 'Last name', 'Email', 'Phone', 'Address', 'City', 'State', 'Postal code']);

        Person::query()
            ->where('directory_opt_in', true)
            ->orderBy('id')
            ->chunkById(500, function ($people) use ($output): void {
                foreach ($people as $person) {
                    fputcsv($output, array_map($this->csvCell(...), [
                        $person->first_name,
                        $person->last_name,
                        $person->email,
                        $person->phone,
                        trim($person->address_line1.' '.$person->address_line2),
                        $person->city,
                        $person->state,
                        $person->postal_code,
                    ]));
                }
            });
    }

    private function writeAttendance($output): void
    {
        fputcsv($output, ['Date', 'Member', 'Service', 'Event', 'Status', 'Headcount']);

        Attendance::query()
            ->with(['person', 'service', 'event'])
            ->when($this->fromDate !== '', fn ($query) => $query->whereDate('attended_on', '>=', $this->fromDate))
            ->when($this->throughDate !== '', fn ($query) => $query->whereDate('attended_on', '<=', $this->throughDate))
            ->orderBy('id')
            ->chunkById(500, function ($attendances) use ($output): void {
                foreach ($attendances as $attendance) {
                    fputcsv($output, array_map($this->csvCell(...), [
                        $attendance->attended_on?->toDateString(),
                        $attendance->person?->full_name,
                        $attendance->service?->name,
                        $attendance->event?->title,
                        $attendance->status,
                        $attendance->headcount,
                    ]));
                }
            });
    }

    private function csvCell(mixed $value): mixed
    {
        if (is_string($value) && preg_match('/^[=+@\\-\t\r]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
