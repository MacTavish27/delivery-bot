<?php

return [
    'token' => env('TELEGRAM_BOT_TOKEN'),

    'config' => [
        'timeout' => 10,
        'cache'   => \SergiX44\Nutgram\Cache\LaravelCache::class,
    ],

    'routes' => base_path('routes/telegram.php'),

    'mixins' => false,

    'log_updates' => env('NUTGRAM_LOG_UPDATES', false),
];
