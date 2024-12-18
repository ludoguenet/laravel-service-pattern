<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $casts = [
        'scheduled_date' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Depot, $this>
     */
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
