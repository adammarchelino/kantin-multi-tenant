<?php

namespace App\Modules\Admin\Services;

use App\Models\DiningTable;
use App\Models\TableQrToken;
use App\Support\Tokens\OpaqueToken;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class QrTokenService
{
    public function __construct(private AuditLogger $audit) {}

    public function issue(DiningTable $table, ?CarbonInterface $expiresAt = null): string
    {
        return DB::transaction(function () use ($table, $expiresAt): string {
            $locked = DiningTable::lockForUpdate()->findOrFail($table->id);
            TableQrToken::where('dining_table_id', $locked->id)->active()->update(['status' => 'revoked', 'revoked_at' => now()]);
            $token = OpaqueToken::issue();
            (new TableQrToken)->forceFill([
                'dining_table_id' => $locked->id, 'token_hash' => $token['hash'], 'status' => 'active',
                'issued_at' => now(), 'expires_at' => $expiresAt, 'revoked_at' => null,
            ])->save();
            $qr = TableQrToken::where('token_hash', $token['hash'])->firstOrFail();
            $this->audit->record('table_qr_token', $qr->id, 'issued', null, ['dining_table_id' => $locked->id], null, $locked->canteen_id);

            return $token['plain'];
        });
    }

    public function rotate(DiningTable $table): string
    {
        return $this->issue($table);
    }
}
