<?php

namespace App\Filament\Admin\Resources\Tenants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nomi')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->label('Slug')
                ->unique(ignoreRecord: true)
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->label('Telefon')
                ->tel(),
            TextInput::make('address')
                ->label('Manzil'),
            Toggle::make('is_active')
                ->label('Faol')
                ->default(true),
        ]);
    }
}
