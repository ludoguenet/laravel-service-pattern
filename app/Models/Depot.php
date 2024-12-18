<?php

namespace App\Models;

use Database\Factories\DepotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Depot extends Model
{
    /** @use HasFactory<DepotFactory> */
    use HasFactory;

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
