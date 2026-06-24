<?php

namespace App\Jobs;

use App\Models\Bot;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SergiX44\Nutgram\Nutgram;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $order = $this->order->load(['botUser', 'items.product', 'tenant']);

        $bot = app(Nutgram::class);

        $this->notifyOperators($bot, $order);
    }

    private function notifyOperators(Nutgram $bot, Order $order): void
    {
        $operators = \App\Models\User::where('tenant_id', $order->tenant_id)
            ->where('role', 'operator')
            ->whereNotNull('telegram_id')
            ->get();

        $itemsList = $order->items->map(function ($item) {
            return "• {$item->product->name} x{$item->quantity} — " .
                number_format($item->price * $item->quantity, 0, '.', ' ') . " so'm";
        })->join("\n");

        $message = "🆕 *Yangi buyurtma #{$order->id}*\n\n" .
            "👤 Mijoz: {$order->botUser->first_name}\n" .
            "📦 Mahsulotlar:\n{$itemsList}\n\n" .
            "💰 Jami: " . number_format($order->total_price, 0, '.', ' ') . " so'm\n" .
            "📍 Manzil: {$order->address}\n\n" .
            "⏰ Vaqt: " . $order->created_at->format('d.m.Y H:i');

        foreach ($operators as $operator) {
            $bot->sendMessage(
                chat_id: $operator->telegram_id,
                text: $message,
                parse_mode: 'Markdown'
            );
        }
    }
}
