<?php

namespace App\Filament\Admin\Resources\Bots\Schemas;

use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tenant_id')
                ->label('Do\'kon')
                ->options(Tenant::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('token')
                ->label('Bot Token')
                ->required()
                ->password()
                ->revealable()
                ->maxLength(255),
            TextInput::make('username')
                ->label('Bot username')
                ->prefix('@')
                ->maxLength(255),
            Toggle::make('is_active')
                ->label('Faol')
                ->default(true),
        ]);
    }
}
