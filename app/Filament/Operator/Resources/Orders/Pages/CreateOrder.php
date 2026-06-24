<?php

namespace App\Filament\Operator\Resources\Orders\Pages;

use App\Filament\Operator\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
