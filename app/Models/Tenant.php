<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = ['canteen_id', 'code', 'slug', 'display_name', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
