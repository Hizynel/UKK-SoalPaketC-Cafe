<?php

namespace App\Filament\Resources\StockMasuks\Pages;

use App\Filament\Resources\StockMasuks\StockMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockMasuks extends ListRecords
{
    protected static string $resource = StockMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
