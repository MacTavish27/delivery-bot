<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderToQueue;
use App\Listeners\SendStatusNotificationToQueue;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Nutgram;

class AppServiceProvider extends ServiceProvider
{
    private static bool $booted = false;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        // Events
        Event::listen(OrderPlaced::class, SendOrderToQueue::class);
        Event::listen(OrderStatusChanged::class, SendStatusNotificationToQueue::class);

        // Observer
        Order::observe(OrderObserver::class);

        // Nutgram routes
        $bot = $this->app->make(Nutgram::class);
        $routes = require base_path('routes/telegram.php');
        $routes($bot);
    }
}
