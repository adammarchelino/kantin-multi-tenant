<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableQrToken extends Model
{
    protected $fillable = ['status'];

    protected function casts(): array
    {
        return ['issued_at' => 'immutable_datetime', 'expires_at' => 'immutable_datetime', 'revoked_at' => 'immutable_datetime'];
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->revoked_at === null && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active')->whereNull('revoked_at');
    }
}
