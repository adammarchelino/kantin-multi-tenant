<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantBankAccount extends Model
{
    protected $fillable = ['account_number_cipher', 'account_last4', 'bank_code', 'account_holder', 'status', 'is_primary', 'verified_by'];

    protected function casts(): array
    {
        return ['account_number_cipher' => 'encrypted', 'is_primary' => 'boolean'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
