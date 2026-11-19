<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Services\CsvImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Data Menu dari CSV')
                ->modalDescription('Unggah CSV master untuk mengimpor menu beserta data bahan baku dan resep.')
                ->form([
                    FileUpload::make('file_csv')
                        ->label('File CSV (Opsional)')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->disk('local')
                        ->directory('csv-imports')
                        ->helperText('Kosongkan file untuk mengimpor dataset bawaan UKK.'),
                ])
                ->modalSubmitActionLabel('Mulai Import')
                ->action(function (array $data): void {
                    $service = app(CsvImportService::class);
                    $hasil = ! empty($data['file_csv'])
                        ? $service->import(Storage::disk('local')->path($data['file_csv']))
                        : $service->importDefault();

                    Notification::make()
                        ->title($hasil['message'])
                        ->{$hasil['ok'] ? 'success' : 'danger'}()
                        ->send();
                }),
        ];
    }
}
