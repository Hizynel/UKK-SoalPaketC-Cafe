<?php

namespace App\Filament\Resources\BahanBakus\Pages;

use App\Filament\Resources\BahanBakus\BahanBakuResource;
use App\Services\SupplierPriceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBahanBakus extends ListRecords
{
    protected static string $resource = BahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('perbaruiHarga')
                ->label('Perbarui Harga dari Supplier')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Perbarui Harga Bahan Baku')
                ->modalDescription('Apakah Anda yakin ingin memperbarui harga semua bahan baku yang sesuai dari API Supplier?')
                ->modalSubmitActionLabel('Perbarui Sekarang')
                ->action(function () {
                    $hasil = app(SupplierPriceService::class)->sync();

                    Notification::make()
                        ->title($hasil['message'])
                        ->{$hasil['ok'] ? 'success' : 'warning'}()
                        ->send();
                }),
        ];
    }
}
