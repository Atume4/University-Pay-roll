<?php

namespace App\Services;

use App\Models\AuditLog;

final class Audit
{
    public static function record(string $action, string $entityType, int $entityId, array $meta = []): void
    {
        // Never record account numbers, passwords, salaries or other secrets in metadata.
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $meta,
        ]);
    }
}
