<?php

namespace App\Observers;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if (!$order->wasChanged('status')) {
            return;
        }

        $cacheKey = "order_status_changed_{$order->id}_{$order->status}";

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, 5);

        OrderStatusChanged::dispatch($order);
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
