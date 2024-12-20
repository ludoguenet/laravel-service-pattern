<?php

declare(strict_types=1);

use App\Events\OrdersProcessed;
use App\Models\Depot;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderProcessor;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseHas;

it('processes orders that are ready and scheduled for today or earlier', function () {
    Event::fake();

    $depot = Depot::factory()->create(['region' => 'North']);
    $user = User::factory()->create();

    $readyOrder = Order::factory()->create([
        'status' => 'ready',
        'scheduled_date' => now(),
        'depot_id' => $depot->id,
        'user_id' => $user->id,
    ]);

    $futureOrder = Order::factory()->create([
        'status' => 'ready',
        'scheduled_date' => now()->addDays(1),
        'depot_id' => $depot->id,
        'user_id' => $user->id,
    ]);

    $processor = new OrderProcessor;

    $processor->processReadyOrders();

    assertDatabaseHas('orders', [
        'id' => $readyOrder->id,
        'status' => 'processing',
    ]);

    assertDatabaseHas('orders', [
        'id' => $futureOrder->id,
        'status' => 'ready',
    ]);

    Event::assertDispatched(OrdersProcessed::class, function ($event) {
        return $event->ordersByRegion->first()->region === 'North' &&
               $event->ordersByRegion->first()->count === 1;
    });
})->only();

it('update orders priority when users are vip-tier', function () {
    $vipUser = User::factory()->set('tier', 'vip')->hasOrders(['priority' => 'high'])->create();
    $baseUser = User::factory()->set('tier', 'base')->hasOrders(['priority' => 'high'])->create();

    $processor = new OrderProcessor;
    $processor->updatePriorities();

    expect($vipUser->orders->first()->fresh()->priority)->toBe('critical');
    expect($baseUser->orders->first()->fresh()->priority)->toBe('high');
})->only();
