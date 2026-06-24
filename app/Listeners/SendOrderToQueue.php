<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\ProcessOrderJob;
use Illuminate\Support\Facades\Cache;

class SendOrderToQueue
{
    public function handle(OrderPlaced $event): void
    {
        $cacheKey = "order_placed_{$event->order->id}";

        if (Cache::has($cacheKey)) {
            \Log::info('OrderPlaced skipped (duplicate)', ['order_id' => $event->order->id]);
            return;
        }

        Cache::put($cacheKey, true, 10);

        \Log::info('OrderPlaced triggered', ['order_id' => $event->order->id]);

        ProcessOrderJob::dispatch($event->order)
            ->onQueue('orders');
    }
}
