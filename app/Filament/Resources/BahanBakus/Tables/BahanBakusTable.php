<?php

namespace App\Filament\Resources\BahanBakus\Tables;

use App\Models\bahan_baku;
use App\Services\SupplierPriceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class BahanBakusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bahan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('satuan'),

                TextColumn::make('stock')
                    ->color(fn ($record) => $record->stock <= $record->batas_minimun ? 'danger' : null),

                TextColumn::make('batas_minimun')
                    ->label('Batas minimum'),

                TextColumn::make('harga_terakhir')
                    ->label('Harga per satuan dasar')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('harga_diperbaharui')
                    ->label('Harga diperbaharui pada')
                    ->date('d M Y')
                    ->placeholder('belum pernah di perbaharui'),
            ])
            ->filters([
                Filter::make('menipis')
                    ->label('Menipis')
                    ->query(fn ($query) => $query->whereColumn('stock', '<=', 'batas_minimun')),
            ])
            ->recordActions([
                Action::make('updateHarga')
                    ->label('Update Harga')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (bahan_baku $record) => "Perbarui Harga {$record->nama_bahan}")
                    ->modalDescription('Ambil harga terbaru untuk bahan ini dari API Supplier?')
                    ->action(function (bahan_baku $record) {
                        $hasil = app(SupplierPriceService::class)->syncSingle($record);

                        Notification::make()
                            ->title($hasil['message'])
                            ->{$hasil['ok'] ? 'success' : 'warning'}()
                            ->send();
                    }),
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
