<?php

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Category;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class MenuHandler
{
    public static function handle(Nutgram $bot): void
    {
        $botModel = Bot::where('token', config('nutgram.token'))->first();

        $categories = Category::where('tenant_id', $botModel->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($categories->isEmpty()) {
            $bot->answerCallbackQuery(text: 'Hozircha kategoriyalar yo\'q.');
            return;
        }

        $keyboard = InlineKeyboardMarkup::make();

        foreach ($categories as $category) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $category->name,
                    callback_data: "category_{$category->id}"
                )
            );
        }

        $bot->editMessageText(
            text: "📂 *Kategoriyani tanlang:*",
            parse_mode: 'Markdown',
            reply_markup: $keyboard
        );

        $bot->answerCallbackQuery();
    }
}
