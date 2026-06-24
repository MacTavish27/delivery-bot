<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SergiX44\Nutgram\Nutgram;

class SendOrderStatusNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $order = $this->order->load('botUser');

        $bot = app(Nutgram::class);

        $message = match ($order->status) {
            'confirmed'  => "✅ Buyurtmangiz tasdiqlandi!\n\n📋 Buyurtma #{$order->id}\n🕐 Tez orada yetkazib beriladi.",
            'delivering' => "🚗 Buyurtmangiz yo'lda!\n\n📋 Buyurtma #{$order->id}\n📍 Kuryer manzilingizga yo'l oldi.",
            'delivered'  => "📦 Buyurtmangiz yetkazildi!\n\n📋 Buyurtma #{$order->id}\n🙏 Xarid uchun rahmat!",
            'cancelled'  => "❌ Buyurtmangiz bekor qilindi.\n\n📋 Buyurtma #{$order->id}\n📞 Savollar uchun operatorga murojaat qiling.",
            default      => null,
        };

        if ($message && $order->botUser?->telegram_id) {
            $bot->sendMessage(
                chat_id: $order->botUser->telegram_id,
                text: $message,
                parse_mode: 'Markdown'
            );
        }
    }
}
