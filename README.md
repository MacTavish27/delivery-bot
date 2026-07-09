# Delivery Bot

A multi-tenant delivery management platform built with Laravel and Filament, combining a web-based admin/operator panel with a Telegram bot for order intake and customer interaction.

## Overview

Delivery Bot lets multiple businesses (tenants) manage their own delivery operations independently through a shared platform. Each tenant's team can process incoming orders, coordinate operators, and communicate with customers via Telegram — all backed by real-time, event-driven notifications.

## Features

- **Multi-tenant architecture** — isolated data and configuration per tenant
- **Multi-panel admin interface** (Filament 5)
    - **Superadmin panel** — manage tenants, global settings, and platform-level configuration
    - **Operator panel** — day-to-day order handling, delivery assignment, and customer communication
- **Role-based access control** via Spatie Laravel-Permission + Filament Shield
- **Telegram bot integration** (Nutgram) for customer-facing order conversations
    - `OrderConversation` flow guides customers through placing and tracking orders directly in Telegram
- **Asynchronous job processing** via RabbitMQ (`vladimir-yuldashev/laravel-queue-rabbitmq`)
- **Event-driven notifications** — order status changes, assignments, and updates propagate in real time to relevant users and Telegram chats
- **Redis** for caching, session storage, and queue backing (separate named connections/database indices per use case)

## Tech Stack

| Layer                          | Technology                                         |
| ------------------------------ | -------------------------------------------------- |
| Framework                      | Laravel 12                                         |
| Admin UI                       | Filament 5                                         |
| Roles & Permissions            | Spatie Laravel-Permission, Filament Shield         |
| Telegram Bot                   | Nutgram                                            |
| Queue                          | RabbitMQ (`php-amqplib`, `laravel-queue-rabbitmq`) |
| Cache / Sessions / Queue store | Redis                                              |
| Frontend build                 | Vite                                               |

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB
- Redis
- RabbitMQ
- Node.js & npm
- PHP extensions: `sockets` (required by `php-amqplib` for RabbitMQ), `exif` (required by Filament Curator, if used)

## Installation

```bash
git clone https://github.com/MacTavish27/delivery-bot.git
cd delivery-bot

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your `.env` file:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=delivery_bot
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3

QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest

TELEGRAM_BOT_TOKEN=your-bot-token-here
```

Run migrations and seed initial data:

```bash
php artisan migrate
php artisan shield:generate --all
php artisan db:seed --class=SuperAdminSeeder
```

Build frontend assets:

```bash
npm run dev
# or for production
npm run build
```

Start the queue worker (RabbitMQ):

```bash
php artisan queue:work rabbitmq
```

Run the local dev server:

```bash
php artisan serve
```

## Panels

| Panel      | URL (default) | Purpose                                 |
| ---------- | ------------- | --------------------------------------- |
| Superadmin | `/admin`      | Tenant management, global configuration |
| Operator   | `/operator`   | Order processing, delivery coordination |

## Roles

Managed via Filament Shield. Default roles include:

- `super_admin` — full platform access across all tenants
- `operator` — tenant-scoped order and delivery management

Roles and permissions can be regenerated after adding new resources:

```bash
php artisan shield:generate --all
php artisan permission:cache-reset
```

## Telegram Bot

The bot uses [Nutgram](https://nutgram.laravel-notification-channels.com/) to handle customer interactions. The core `OrderConversation` class walks customers through:

1. Starting a new order
2. Collecting delivery details
3. Confirming and submitting the order
4. Receiving real-time status updates as the order progresses

## Development Notes

- If `composer update` fails on `ext-sockets`, enable the `sockets` extension in `php.ini` (required by `php-amqplib` for RabbitMQ support).
- Redis is configured with separate database indices for cache, session, and queue to avoid key collisions between subsystems.
- After seeding roles/permissions, always run `php artisan permission:cache-reset` — Spatie's permission cache does not refresh automatically within the same request/session.

## License

MIT License.
