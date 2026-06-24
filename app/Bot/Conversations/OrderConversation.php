<?php

namespace App\Bot\Conversations;

use App\Events\OrderPlaced;
use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class OrderConversation extends Conversation
{
    protected int $productId;
    protected ?int $quantity = null;
    protected ?string $address = null;

    public function start(Nutgram $bot): void
    {
        $this->productId = $bot->get('product_id');

        $product = Product::find($this->productId);

        $bot->answerCallbackQuery();
        $bot->sendMessage(
            text: "🛒 *{$product->name}*\n💰 Narxi: " . number_format($product->price, 0, '.', ' ') . " so'm\n\nNechta dona kerak?",
            parse_mode: 'Markdown',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('1', callback_data: 'qty_1'),
                    InlineKeyboardButton::make('2', callback_data: 'qty_2'),
                    InlineKeyboardButton::make('3', callback_data: 'qty_3'),
                )
                ->addRow(
                    InlineKeyboardButton::make('❌ Bekor qilish', callback_data: 'cancel')
                )
        );

        $this->next('askAddress');
    }

    public function askAddress(Nutgram $bot): void
    {
        $callbackData = $bot->callbackQuery()?->data;

        if ($callbackData === 'cancel') {
            $bot->answerCallbackQuery();
            $bot->sendMessage('❌ Buyurtma bekor qilindi.');
            $this->end();
            return;
        }

        if (str_starts_with($callbackData ?? '', 'qty_')) {
            $this->quantity = (int) str_replace('qty_', '', $callbackData);
            $bot->answerCallbackQuery();
        }

        $bot->sendMessage(
            text: "📍 Yetkazib berish manzilingizni yozing:",
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
                one_time_keyboard: true
            )->addRow(
                KeyboardButton::make(
                    text: '📍 Joylashuvimni yuborish',
                    request_location: true
                )
            )
        );

        $this->next('confirmOrder');
    }

    public function confirmOrder(Nutgram $bot): void
    {
        if ($bot->message()?->location) {
            $location = $bot->message()->location;
            $this->address = "📍 {$location->latitude}, {$location->longitude}";
        } else {
            $this->address = $bot->message()?->text;
        }

        $product = Product::find($this->productId);
        $totalPrice = $product->price * $this->quantity;

        $bot->sendMessage(
            text: "✅ *Buyurtmangizni tasdiqlang:*\n\n" .
                "🛍 Mahsulot: {$product->name}\n" .
                "🔢 Miqdor: {$this->quantity} dona\n" .
                "💰 Jami: " . number_format($totalPrice, 0, '.', ' ') . " so'm\n" .
                "📍 Manzil: {$this->address}",
            parse_mode: 'Markdown',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('✅ Tasdiqlash', callback_data: 'confirm'),
                    InlineKeyboardButton::make('❌ Bekor', callback_data: 'cancel')
                )
        );

        $this->next('saveOrder');
    }

    public function saveOrder(Nutgram $bot): void
    {
        $callbackData = $bot->callbackQuery()?->data;

        if ($callbackData === 'cancel') {
            $bot->answerCallbackQuery();
            $bot->sendMessage('❌ Buyurtma bekor qilindi.');
            $this->end();
            return;
        }

        $product = Product::find($this->productId);
        $totalPrice = $product->price * $this->quantity;

        $botModel = Bot::where('token', config('nutgram.token'))->first();
        $botUser = BotUser::where('telegram_id', $bot->userId())->first();

        $order = Order::create([
            'tenant_id'   => $botModel->tenant_id,
            'bot_user_id' => $botUser->id,
            'status'      => 'new',
            'total_price' => $totalPrice,
            'address'     => $this->address,
        ]);

        // Event dispatch
        OrderPlaced::dispatch($order);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $this->productId,
            'quantity'   => $this->quantity,
            'price'      => $product->price,
        ]);

        $bot->answerCallbackQuery();

        $this->end();
    }
}
