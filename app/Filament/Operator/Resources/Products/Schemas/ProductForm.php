<?php

namespace App\Filament\Operator\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')
                ->label('Kategoriya')
                ->options(
                    Category::where('tenant_id', auth()->user()->tenant_id)
                        ->where('is_active', true)
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->required(),
            TextInput::make('name')
                ->label('Nomi')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Tavsif')
                ->rows(3),
            TextInput::make('price')
                ->label('Narxi')
                ->numeric()
                ->suffix('so\'m')
                ->required(),
            FileUpload::make('image')
                ->label('Rasm')
                ->image()
                ->directory('products')
                ->maxSize(2048),
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
