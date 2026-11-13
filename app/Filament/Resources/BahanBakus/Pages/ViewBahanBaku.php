<?php

namespace App\Filament\Resources\BahanBakus\Pages;

use App\Filament\Resources\BahanBakus\BahanBakuResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBahanBaku extends ViewRecord
{
    protected static string $resource = BahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
