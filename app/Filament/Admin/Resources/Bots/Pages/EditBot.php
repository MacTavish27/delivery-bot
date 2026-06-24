<?php

namespace App\Filament\Admin\Resources\Bots\Pages;

use App\Filament\Admin\Resources\Bots\BotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBot extends EditRecord
{
    protected static string $resource = BotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
