<?php

namespace App\Filament\Resources\StockMasuks\Pages;

use App\Filament\Resources\StockMasuks\StockMasukResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockMasuk extends ViewRecord
{
    protected static string $resource = StockMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
