<?php

use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Tenant ───────────────────────────────────────────────────────────────────

test('tenant has many bots', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 't', 'is_active' => true]);
    Bot::create(['tenant_id' => $tenant->id, 'token' => 'abc:123', 'username' => 'bot1', 'is_active' => true]);

    expect($tenant->bots)->toHaveCount(1);
    expect($tenant->bots->first())->toBeInstanceOf(Bot::class);
});

test('tenant has many products', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 't2', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Food', 'is_active' => true]);
    Product::create([
        'tenant_id' => $tenant->id,
        'category_id' => $category->id,
        'name' => 'Pizza',
        'price' => 25000,
        'is_active' => true,
    ]);

    expect($tenant->products)->toHaveCount(1);
    expect($tenant->products->first()->name)->toBe('Pizza');
});

test('tenant has many orders', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 't3', 'is_active' => true]);
    $botUser = BotUser::create(['tenant_id' => $tenant->id, 'telegram_id' => 1, 'first_name' => 'A']);
    Order::create(['tenant_id' => $tenant->id, 'bot_user_id' => $botUser->id, 'status' => 'new', 'total_price' => 0, 'address' => 'Test address']);

    expect($tenant->orders)->toHaveCount(1);
});

// ─── Bot ─────────────────────────────────────────────────────────────────────

test('bot belongs to tenant', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'bt', 'is_active' => true]);
    $bot = Bot::create(['tenant_id' => $tenant->id, 'token' => 'x:1', 'username' => 'b', 'is_active' => true]);

    expect($bot->tenant)->toBeInstanceOf(Tenant::class);
    expect($bot->tenant->id)->toBe($tenant->id);
});

// ─── Category ─────────────────────────────────────────────────────────────────

test('category belongs to tenant', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'cat', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Drinks', 'is_active' => true]);

    expect($category->tenant)->toBeInstanceOf(Tenant::class);
});

test('category has many products', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'cats', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Drinks', 'is_active' => true]);
    Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'name' => 'Tea', 'price' => 5000, 'is_active' => true]);

    expect($category->products)->toHaveCount(1);
    expect($category->products->first()->name)->toBe('Tea');
});

// ─── Product ─────────────────────────────────────────────────────────────────

test('product belongs to category and tenant', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'prod', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Cat', 'is_active' => true]);
    $product = Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'name' => 'Burger', 'price' => 20000, 'is_active' => true]);

    expect($product->category)->toBeInstanceOf(Category::class);
    expect($product->tenant)->toBeInstanceOf(Tenant::class);
});

// ─── Order ────────────────────────────────────────────────────────────────────

test('order belongs to tenant and botUser', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'ord', 'is_active' => true]);
    $botUser = BotUser::create(['tenant_id' => $tenant->id, 'telegram_id' => 99, 'first_name' => 'B']);
    $order = Order::create(['tenant_id' => $tenant->id, 'bot_user_id' => $botUser->id, 'status' => 'new', 'total_price' => 0, 'address' => 'Test address']);

    expect($order->tenant)->toBeInstanceOf(Tenant::class);
    expect($order->botUser)->toBeInstanceOf(BotUser::class);
});

test('order has many items', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'ordi', 'is_active' => true]);
    $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'C', 'is_active' => true]);
    $product = Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'name' => 'P', 'price' => 1000, 'is_active' => true]);
    $botUser = BotUser::create(['tenant_id' => $tenant->id, 'telegram_id' => 88, 'first_name' => 'C']);
    $order = Order::create(['tenant_id' => $tenant->id, 'bot_user_id' => $botUser->id, 'status' => 'new', 'total_price' => 1000, 'address' => 'Test address']);
    OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => 500]);

    expect($order->items)->toHaveCount(1);
    expect($order->items->first())->toBeInstanceOf(OrderItem::class);
});

// ─── BotUser ─────────────────────────────────────────────────────────────────

test('botUser belongs to tenant and has many orders', function () {
    $tenant = Tenant::create(['name' => 'T', 'slug' => 'bu', 'is_active' => true]);
    $botUser = BotUser::create(['tenant_id' => $tenant->id, 'telegram_id' => 77, 'first_name' => 'D']);
    Order::create(['tenant_id' => $tenant->id, 'bot_user_id' => $botUser->id, 'status' => 'new', 'total_price' => 0, 'address' => 'Test address']);

    expect($botUser->tenant)->toBeInstanceOf(Tenant::class);
    expect($botUser->orders)->toHaveCount(1);
});

// ─── User ─────────────────────────────────────────────────────────────────────

test('super_admin user can access admin panel but not operator panel', function () {
    $user = User::factory()->create(['role' => 'super_admin']);

    $adminPanel = Mockery::mock(\Filament\Panel::class)->makePartial();
    $adminPanel->shouldReceive('getId')->andReturn('admin');

    $operatorPanel = Mockery::mock(\Filament\Panel::class)->makePartial();
    $operatorPanel->shouldReceive('getId')->andReturn('operator');

    expect($user->canAccessPanel($adminPanel))->toBeTrue();
    expect($user->canAccessPanel($operatorPanel))->toBeFalse();
});

test('operator user can access operator panel but not admin panel', function () {
    $user = User::factory()->create(['role' => 'operator']);

    $adminPanel = Mockery::mock(\Filament\Panel::class)->makePartial();
    $adminPanel->shouldReceive('getId')->andReturn('admin');

    $operatorPanel = Mockery::mock(\Filament\Panel::class)->makePartial();
    $operatorPanel->shouldReceive('getId')->andReturn('operator');

    expect($user->canAccessPanel($operatorPanel))->toBeTrue();
    expect($user->canAccessPanel($adminPanel))->toBeFalse();
});
