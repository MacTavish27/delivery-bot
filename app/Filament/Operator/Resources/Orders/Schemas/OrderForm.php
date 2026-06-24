<?php

namespace App\Filament\Operator\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label('Holat')
                ->options([
                    'new'        => '🆕 Yangi',
                    'confirmed'  => '✅ Tasdiqlangan',
                    'delivering' => '🚗 Yetkazilmoqda',
                    'delivered'  => '📦 Yetkazildi',
                    'cancelled'  => '❌ Bekor qilindi',
                ])
                ->required(),
            TextInput::make('total_price')
                ->label('Summa')
                ->disabled()
                ->suffix('so\'m'),
            Textarea::make('address')
                ->label('Manzil')
                ->disabled()
                ->rows(2),
            Textarea::make('comment')
                ->label('Izoh')
                ->disabled()
                ->rows(2),
        ]);
    }
}
