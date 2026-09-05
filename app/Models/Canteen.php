<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canteen extends Model
{
    protected $fillable = ['code', 'name', 'slug'];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
