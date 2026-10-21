<?php

namespace App\Modules\Admin\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class AuditLogger
{
    private const BLOCKED = ['password', 'account_number', 'account_number_cipher', 'secret', 'token', 'recovery_code'];

    public function record(string $entity, int|string|null $entityId, string $action, ?array $before, ?array $after, ?int $tenantId = null, ?int $canteenId = null): AuditLog
    {
        return AuditLog::create([
            'actor_id' => Auth::id(), 'canteen_id' => $canteenId, 'tenant_id' => $tenantId,
            'entity' => $entity, 'entity_id' => $entityId === null ? null : (string) $entityId,
            'action' => $action, 'request_id' => request()->header('X-Request-Id') ?: (string) Str::uuid(),
            'before' => $this->sanitize($before), 'after' => $this->sanitize($after), 'logged_at' => now(),
        ]);
    }

    private function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }
        foreach ($values as $key => $value) {
            $normalizedKey = Str::snake((string) $key);
            if (collect(self::BLOCKED)->contains(fn (string $sensitive) => Str::contains($normalizedKey, $sensitive))) {
                unset($values[$key]);
            } elseif (is_array($value)) {
                $values[$key] = $this->sanitize($value);
            }
        }

        return $values;
    }
}
