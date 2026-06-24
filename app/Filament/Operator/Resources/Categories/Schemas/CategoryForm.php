<?php

namespace App\Filament\Operator\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nomi')
                ->required()
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('Tartib')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('Faol')
                ->default(true),
        ]);
    }
}
