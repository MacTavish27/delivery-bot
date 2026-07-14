<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use SergiX44\Nutgram\Testing\FakeNutgram;

uses(RefreshDatabase::class);

test('home page returns successful response', function () {
    $this->get('/')->assertSuccessful();
});

test('admin route redirects unauthenticated users to login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('admin login page renders without errors', function () {
    $this->withoutVite()->get('/admin/login')->assertSuccessful();
});

test('operator login page renders without errors', function () {
    $this->withoutVite()->get('/operator/login')->assertSuccessful();
});

test('webhook endpoint requires a POST request', function () {
    $this->get('/webhook/telegram')->assertMethodNotAllowed();
});

test('bot responds to /start command when bot is not configured', function () {
    $update = [
        'update_id' => 123456789,
        'message' => [
            'message_id' => 1,
            'date' => time(),
            'chat' => ['id' => 123, 'type' => 'private'],
            'from' => ['id' => 123, 'is_bot' => false, 'first_name' => 'Test'],
            'text' => '/start',
        ],
    ];

    $bot = FakeNutgram::instance($update);

    (require base_path('routes/telegram.php'))($bot);

    $bot->run();

    $bot->assertReplyText('Bot sozlanmagan.');
});
