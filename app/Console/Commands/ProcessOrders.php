<?php

namespace App\Console\Commands;

use App\Services\OrderProcessor;
use Illuminate\Console\Command;

class ProcessOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes orders that are ready and scheduled for today or earlier';

    public function handle(OrderProcessor $service): void
    {
        $this->info('Starting order processing...');

        try {
            $service->processReadyOrders();
            $this->info('Orders processed successfully.');
        } catch (\Exception $e) {
            $this->error('Error processing orders: '.$e->getMessage());
        }
    }
}
