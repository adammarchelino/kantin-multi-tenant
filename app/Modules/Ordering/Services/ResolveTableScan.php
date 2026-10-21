<?php

namespace App\Modules\Ordering\Services;

use App\Models\Canteen;
use App\Models\CustomerSession;
use App\Models\DiningTable;
use App\Models\TableQrToken;
use App\Modules\Catalog\Services\TenantOpeningHours;
use App\Support\Tokens\OpaqueToken;

final class ResolveTableScan
{
    public function __construct(private TenantOpeningHours $hours) {}

    /** @return array{status:string,canteen:Canteen,table:DiningTable,plain?:string}|null */
    public function resolve(string $plainToken): ?array
    {
        $qr = TableQrToken::where('token_hash', OpaqueToken::hash($plainToken))->with('diningTable.canteen')->first();
        if (! $qr || ! $qr->isActive()) {
            return null;
        }
        $table = $qr->diningTable;
        $canteen = $table?->canteen;
        if (! $table || ! $table->isActive() || ! $canteen || $canteen->status !== 'active') {
            return null;
        }
        if (! $this->hours->canteenIsOpen($canteen)) {
            return ['status' => 'closed', 'canteen' => $canteen, 'table' => $table];
        }

        $sessionToken = OpaqueToken::issue();
        (new CustomerSession)->forceFill([
            'canteen_id' => $canteen->id, 'dining_table_id' => $table->id, 'qr_token_id' => $qr->id,
            'session_token_hash' => $sessionToken['hash'], 'status' => 'active', 'expires_at' => now()->addHours(4),
        ])->save();

        return ['status' => 'ok', 'canteen' => $canteen, 'table' => $table, 'plain' => $sessionToken['plain']];
    }
}
