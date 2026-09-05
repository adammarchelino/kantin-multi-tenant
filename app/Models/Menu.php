<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = ['tenant_id', 'category_id', 'name', 'price_amount', 'is_available'];

    protected function casts(): array
    {
        return [
            'price_amount' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
