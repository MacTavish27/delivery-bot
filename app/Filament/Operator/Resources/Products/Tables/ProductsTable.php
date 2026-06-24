<?php

namespace App\Filament\Operator\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Rasm')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategoriya')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Narxi')
                    ->numeric()
                    ->suffix(' so\'m')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Tartib')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Faol')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Yaratildi')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name'),
                SelectFilter::make('is_active')
                    ->label('Holat')
                    ->options([
                        '1' => 'Faol',
                        '0' => 'Nofaol',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
