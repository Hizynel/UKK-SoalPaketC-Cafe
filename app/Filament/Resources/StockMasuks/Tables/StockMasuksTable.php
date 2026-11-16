<?php

namespace App\Filament\Resources\StockMasuks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
// use Filament\Actions\ForceDeleteBulkAction;
// use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
// use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class StockMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_pembelian')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('bahanBaku.nama_bahan')->label('Bahan')->searchable(),
                TextColumn::make('jumlah'),
            ])
            ->defaultSort('tanggal_pembelian', 'desc')
                        
            // ->filters([
            //     TrashedFilter::make(),
            // ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                    BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ]);
    }
}
