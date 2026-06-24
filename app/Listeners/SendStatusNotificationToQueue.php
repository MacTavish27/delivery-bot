<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendOrderStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendStatusNotificationToQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $cacheKey = "status_notification_{$event->order->id}_{$event->order->status}";

        if (Cache::has($cacheKey)) {
            \Log::info('Listener skipped (duplicate)', ['order_id' => $event->order->id]);
            return;
        }

        Cache::put($cacheKey, true, 10);

        \Log::info('Listener triggered', ['order_id' => $event->order->id]);

        SendOrderStatusNotification::dispatch($event->order)
            ->onQueue('orders');
    }
}
