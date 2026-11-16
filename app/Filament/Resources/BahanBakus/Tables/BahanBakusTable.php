<?php

namespace App\Filament\Resources\BahanBakus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class BahanBakusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('nama_bahan')->searchable()->sortable(),
            TextColumn::make('satuan'),
            TextColumn::make('stock')->color(fn ($record) => $record->stock <= $record->batas_minimun ? 'danger' : null),
            TextColumn::make('batas_minimun')->label('Batas minimum'),
            TextColumn::make('harga_terakhir')->label('Harga per satuan dasar')->money('IDR', locale: 'id'),
        ])
        ->filters([
            Filter::make('menipis')
                ->label('Menipis')
                ->query(fn ($query) => $query->whereColumn('stock', '<=', 'batas_minimun')),
        ])
            ->recordActions([
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
