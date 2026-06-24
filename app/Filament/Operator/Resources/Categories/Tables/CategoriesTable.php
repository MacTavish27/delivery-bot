<?php

namespace App\Filament\Operator\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable()
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
                //
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
