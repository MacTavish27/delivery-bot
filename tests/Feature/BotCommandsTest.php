<?php

use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SergiX44\Nutgram\Testing\FakeNutgram;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeUpdate(string $text = '/start', int $userId = 100, string $firstName = 'Tester'): array
{
    return [
        'update_id' => random_int(1, 999999),
        'message' => [
            'message_id' => 1,
            'date' => time(),
            'chat' => ['id' => $userId, 'type' => 'private'],
            'from' => ['id' => $userId, 'is_bot' => false, 'first_name' => $firstName],
            'text' => $text,
        ],
    ];
}

function makeCallbackUpdate(string $callbackData, int $userId = 100): array
{
    return [
        'update_id' => random_int(1, 999999),
        'callback_query' => [
            'id' => (string) random_int(1, 999999),
            'from' => ['id' => $userId, 'is_bot' => false, 'first_name' => 'Tester'],
            'message' => [
                'message_id' => 10,
                'date' => time(),
                'chat' => ['id' => $userId, 'type' => 'private'],
                'text' => 'Menyu',
            ],
            'chat_instance' => 'chat_instance_123',
            'data' => $callbackData,
        ],
    ];
}

function bootBot(array $update): FakeNutgram
{
    $bot = FakeNutgram::instance($update);
    (require base_path('routes/telegram.php'))($bot);

    return $bot;
}

// ─── /start command ───────────────────────────────────────────────────────────

test('/start replies with error message when no bot is configured', function () {
    $bot = bootBot(makeUpdate('/start'));

    $bot->run();

    $bot->assertReplyText('Bot sozlanmagan.');
});

test('/start creates a BotUser and sends welcome message when bot is configured', function () {
    $tenant = Tenant::create([
        'name' => 'Test Shop',
        'slug' => 'test-shop',
        'is_active' => true,
    ]);

    $botRecord = Bot::create([
        'tenant_id' => $tenant->id,
        'token' => config('nutgram.token'),
        'username' => 'test_bot',
        'is_active' => true,
    ]);

    expect(BotUser::count())->toBe(0);

    $bot = bootBot(makeUpdate('/start', userId: 42, firstName: 'Ali'));
    $bot->run();

    expect(BotUser::count())->toBe(1);
    expect(BotUser::first()->telegram_id)->toBe(42);
    expect(BotUser::first()->first_name)->toBe('Ali');

    $bot->assertReplyText("Assalomu alaykum, Ali! 👋\n\n🏪 *Test Shop* ga xush kelibsiz!\n\nBuyurtma berish uchun quyidagi tugmani bosing:");
});

test('/start does not duplicate BotUser on repeated calls', function () {
    $tenant = Tenant::create([
        'name' => 'Shop',
        'slug' => 'shop',
        'is_active' => true,
    ]);

    Bot::create([
        'tenant_id' => $tenant->id,
        'token' => config('nutgram.token'),
        'username' => 'test_bot',
        'is_active' => true,
    ]);

    bootBot(makeUpdate('/start', userId: 55))->run();
    bootBot(makeUpdate('/start', userId: 55))->run();

    expect(BotUser::where('telegram_id', 55)->count())->toBe(1);
});

// ─── menu callback ────────────────────────────────────────────────────────────

test('menu callback answers with empty categories message when no categories exist', function () {
    $tenant = Tenant::create([
        'name' => 'Empty Shop',
        'slug' => 'empty-shop',
        'is_active' => true,
    ]);

    Bot::create([
        'tenant_id' => $tenant->id,
        'token' => config('nutgram.token'),
        'username' => 'test_bot',
        'is_active' => true,
    ]);

    $bot = bootBot(makeCallbackUpdate('menu'));
    $bot->run();

    $bot->assertReply('answerCallbackQuery', ['text' => "Hozircha kategoriyalar yo'q."]);
});
