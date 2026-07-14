<?php

use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SergiX44\Nutgram\Testing\FakeNutgram;

uses(RefreshDatabase::class);

// ─── Helpers ──────────────────────────────────────────────────────────────────

function makeStartUpdate(int $userId = 1, string $firstName = 'Ali'): array
{
    return [
        'update_id' => 1,
        'message' => [
            'message_id' => 1,
            'date' => time(),
            'chat' => ['id' => $userId, 'type' => 'private'],
            'from' => ['id' => $userId, 'is_bot' => false, 'first_name' => $firstName],
            'text' => '/start',
        ],
    ];
}

function makeCategoryCallbackUpdate(int $categoryId, int $userId = 1): array
{
    return [
        'update_id' => 2,
        'callback_query' => [
            'id' => '456',
            'from' => ['id' => $userId, 'is_bot' => false, 'first_name' => 'Ali'],
            'message' => [
                'message_id' => 10,
                'date' => time(),
                'chat' => ['id' => $userId, 'type' => 'private'],
                'text' => 'Menyu',
            ],
            'chat_instance' => 'chat_instance_123',
            'data' => "category_{$categoryId}",
        ],
    ];
}

function bootBotForTest(array $update): FakeNutgram
{
    $bot = FakeNutgram::instance($update);
    (require base_path('routes/telegram.php'))($bot);

    return $bot;
}

// ─── /start command ───────────────────────────────────────────────────────────

test('/start sends error when bot is not configured in database', function () {
    $bot = bootBotForTest(makeStartUpdate());
    $bot->run();

    $bot->assertReplyText('Bot sozlanmagan.');
});

test('/start sends welcome message with shop name when configured', function () {
    $tenant = Tenant::create(['name' => 'Dono Burger', 'slug' => 'dono', 'is_active' => true]);
    Bot::create(['tenant_id' => $tenant->id, 'token' => config('nutgram.token'), 'username' => 'dono_bot', 'is_active' => true]);

    $bot = bootBotForTest(makeStartUpdate(userId: 1, firstName: 'Ali'));
    $bot->run();

    $bot->assertReplyText("Assalomu alaykum, Ali! 👋\n\n🏪 *Dono Burger* ga xush kelibsiz!\n\nBuyurtma berish uchun quyidagi tugmani bosing:");
});

// ─── category callback ────────────────────────────────────────────────────────

test('category callback answers with empty message when category has no products', function () {
    $tenant = Tenant::create(['name' => 'Shop', 'slug' => 'shop-cat', 'is_active' => true]);
    Bot::create(['tenant_id' => $tenant->id, 'token' => config('nutgram.token'), 'username' => 'shop_bot', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Drinks', 'is_active' => true]);

    $bot = bootBotForTest(makeCategoryCallbackUpdate($category->id));
    $bot->run();

    $bot->assertReply('answerCallbackQuery', ['text' => "Bu kategoriyada mahsulotlar yo'q."]);
});

test('category callback shows products when they exist', function () {
    $tenant = Tenant::create(['name' => 'Shop2', 'slug' => 'shop2-cat', 'is_active' => true]);
    Bot::create(['tenant_id' => $tenant->id, 'token' => config('nutgram.token'), 'username' => 'shop2_bot', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Food', 'is_active' => true]);
    Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'name' => 'Burger', 'price' => 25000, 'is_active' => true]);

    $bot = bootBotForTest(makeCategoryCallbackUpdate($category->id));
    $bot->run();

    $bot->assertCalled('answerCallbackQuery');
});
