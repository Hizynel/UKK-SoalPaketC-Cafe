<?php

namespace App\Filament\Resources\Reseps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['menu', 'bahanBaku']))
            ->columns([
                TextColumn::make('menu.nama_menu')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bahanBaku.nama_bahan')
                    ->label('Bahan Baku')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('takaran')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('bahanBaku.satuan')
                    ->label('Satuan'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
