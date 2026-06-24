<?php

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Product;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CategoryHandler
{
    public static function handle(Nutgram $bot, int $categoryId): void
    {
        $botModel = Bot::where('token', config('nutgram.token'))->first();

        $products = Product::where('tenant_id', $botModel->tenant_id)
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($products->isEmpty()) {
            $bot->answerCallbackQuery(text: 'Bu kategoriyada mahsulotlar yo\'q.');
            return;
        }

        $keyboard = InlineKeyboardMarkup::make();

        foreach ($products as $product) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: "{$product->name} — " . number_format($product->price, 0, '.', ' ') . " so'm",
                    callback_data: "product_{$product->id}"
                )
            );
        }

        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: '⬅️ Orqaga',
                callback_data: 'menu'
            )
        );

        $bot->editMessageText(
            text: "🛍 *Mahsulotni tanlang:*",
            parse_mode: 'Markdown',
            reply_markup: $keyboard
        );

        $bot->answerCallbackQuery();
    }
}
