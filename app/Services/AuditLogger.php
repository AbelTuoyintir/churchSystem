<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    private const AUDITED_TABLES = [
        'contributions',
        'funds',
        'pledges',
        'pastoral_care_notes',
        'users',
        'roles',
        'model_has_roles',
        'role_has_permissions',
    ];

    public function record(
        string $action,
        ?string $subjectType,
        ?string $subjectId,
        string $description,
        array $metadata = [],
    ): ?AuditLog {
        $request = request();

        try {
            return AuditLog::create([
                'actor_id' => Auth::id(),
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'description' => $description,
                'metadata' => $metadata === [] ? null : $metadata,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging block the primary database operation.
            report($e);

            return null;
        }
    }

    public function recordQuery(QueryExecuted $query): void
    {
        try {
            if (app()->runningInConsole() && ! app()->runningUnitTests()) {
                return;
            }

            if (! Auth::guard()->hasUser()) {
                return;
            }

            $verb = strtolower((string) strtok(ltrim($query->sql), " \t\n\r"));
            $action = match ($verb) {
                'select' => 'read',
                'insert', 'replace' => 'create',
                'update' => 'update',
                'delete' => 'delete',
                default => null,
            };

            if ($action === null) {
                return;
            }

            foreach ($this->auditedTablesIn($query->sql, $verb) as $table) {
                $this->record(
                    $action,
                    $table,
                    null,
                    "Database {$action} on {$table}",
                    ['duration_ms' => $query->time],
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function auditedTablesIn(string $sql, string $verb): array
    {
        $keyword = match ($verb) {
            'select' => '(?:from|join)',
            'insert', 'replace' => 'into',
            'update' => 'update',
            'delete' => 'from',
            default => null,
        };

        if ($keyword === null) {
            return [];
        }

        $tables = [];

        foreach (self::AUDITED_TABLES as $table) {
            if (preg_match('/\\b'.$keyword.'\\s+[`"]?'.preg_quote($table, '/').'[`"]?(?![A-Za-z0-9_])/i', $sql) === 1) {
                $tables[] = $table;
            }
        }

        return $tables;
    }
}
