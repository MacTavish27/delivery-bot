<?php

namespace App\Bot\Commands;

use App\Models\BotUser;
use App\Models\Bot;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;

class StartCommand extends Command
{
    protected string $command = 'start';
    protected ?string $description = 'Botni ishga tushirish';

    public function handle(Nutgram $bot): void
    {
        $botModel = Bot::where('token', config('nutgram.token'))
            ->with('tenant')
            ->first();

        if (!$botModel) {
            $bot->sendMessage('Bot sozlanmagan.');
            return;
        }

        $user = $bot->user();

        BotUser::updateOrCreate(
            ['telegram_id' => $user->id],
            [
                'tenant_id'  => $botModel->tenant_id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name ?? null,
                'username'   => $user->username ?? null,
            ]
        );

        $bot->sendMessage(
            text: "Assalomu alaykum, {$user->first_name}! 👋\n\n" .
                "🏪 *{$botModel->tenant->name}* ga xush kelibsiz!\n\n" .
                "Buyurtma berish uchun quyidagi tugmani bosing:",
            parse_mode: 'Markdown',
            reply_markup: \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup::make()
                ->addRow(
                    \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make(
                        text: '🛍 Menyu',
                        callback_data: 'menu'
                    )
                )
        );
    }
}
