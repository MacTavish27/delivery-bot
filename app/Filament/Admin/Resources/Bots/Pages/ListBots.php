<?php

namespace App\Filament\Admin\Resources\Bots\Pages;

use App\Filament\Admin\Resources\Bots\BotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBots extends ListRecords
{
    protected static string $resource = BotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
