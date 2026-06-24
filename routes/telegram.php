<?php

use App\Bot\Commands\StartCommand;
use App\Bot\Conversations\OrderConversation;
use App\Bot\Handlers\CategoryHandler;
use App\Bot\Handlers\MenuHandler;
use SergiX44\Nutgram\Nutgram;

return function (Nutgram $bot) {

    $bot->onCommand('start', StartCommand::class);

    $bot->onCallbackQueryData('menu', function (Nutgram $bot) {
        MenuHandler::handle($bot);
    });

    $bot->onCallbackQueryData('category_(\d+)', function (Nutgram $bot, int $categoryId) {
        CategoryHandler::handle($bot, $categoryId);
    });

    $bot->onCallbackQueryData('product_(\d+)', function (Nutgram $bot, int $productId) {
        $bot->set('product_id', $productId);
        OrderConversation::begin(
            bot: $bot,
            userId: $bot->userId(),
            chatId: $bot->chatId(),
        );
    });
};
