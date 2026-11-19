<?php

namespace App\Filament\Resources\BahanBakus\Pages;

use App\Filament\Resources\BahanBakus\BahanBakuResource;
use App\Services\CsvImportService;
use App\Services\SupplierPriceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListBahanBakus extends ListRecords
{
    protected static string $resource = BahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Data Bahan, Menu & Resep dari CSV')
                ->form([
                    FileUpload::make('file_csv')
                        ->label('File CSV (Opsional)')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->disk('local')
                        ->directory('csv-imports')
                        ->helperText('Jika tidak memilih file, sistem otomatis mengimpor data bawaan.'),
                ])
                ->modalSubmitActionLabel('Mulai Import')
                ->action(function (array $data) {
                    $service = app(CsvImportService::class);

                    if (! empty($data['file_csv'])) {
                        $path = Storage::disk('local')->path($data['file_csv']);
                        $hasil = $service->import($path);
                    } else {
                        $hasil = $service->importDefault();
                    }

                    Notification::make()
                        ->title($hasil['message'])
                        ->{$hasil['ok'] ? 'success' : 'danger'}()
                        ->send();
                }),

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
