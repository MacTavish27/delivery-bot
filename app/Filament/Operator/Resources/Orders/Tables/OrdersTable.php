<?php

namespace App\Filament\Operator\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('10s')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('botUser.first_name')
                    ->label('Mijoz')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Holat')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new'        => 'info',
                        'confirmed'  => 'warning',
                        'delivering' => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'new'        => '🆕 Yangi',
                        'confirmed'  => '✅ Tasdiqlangan',
                        'delivering' => '🚗 Yetkazilmoqda',
                        'delivered'  => '📦 Yetkazildi',
                        'cancelled'  => '❌ Bekor qilindi',
                    }),
                TextColumn::make('total_price')
                    ->label('Summa')
                    ->numeric()
                    ->suffix(' so\'m')
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Manzil')
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label('Vaqt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Holat')
                    ->options([
                        'new'        => 'Yangi',
                        'confirmed'  => 'Tasdiqlangan',
                        'delivering' => 'Yetkazilmoqda',
                        'delivered'  => 'Yetkazildi',
                        'cancelled'  => 'Bekor qilindi',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
