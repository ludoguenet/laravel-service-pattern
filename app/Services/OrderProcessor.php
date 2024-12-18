<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\OrdersProcessed;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderProcessor
{
    public function processReadyOrders(): void
    {
        $orders = Order::where('status', 'ready')
            ->where('scheduled_date', '<=', now())
            ->get();

        DB::transaction(function () use ($orders) {
            $orders->toQuery()->update([
                'status' => 'processing',
                'processed_at' => now(),
            ]);

            /** @var Collection<array-key, string> $ordersByRegion */
            $ordersByRegion = $orders->toQuery()
                ->join('depots', 'orders.depot_id', '=', 'depots.id')
                ->select('depots.region', DB::raw('count(*) as count'))
                ->groupBy('region')
                ->get();

            event(new OrdersProcessed($ordersByRegion));
        });
    }

    public function updatePriorities(): void
    {
        $urgentOrders = Order::where('priority', 'high')->get();

        $urgentOrders->toQuery()
            ->select('orders.*', 'users.tier')
            ->join('users', 'orders.customer_id', '=', 'users.id')
            ->where('users.tier', 'vip')
            ->update(['priority' => 'critical']);
    }
}
