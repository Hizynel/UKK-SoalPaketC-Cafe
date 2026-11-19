<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn ($query) => $query->with('reseps.bahanBaku'))
            ->columns([
                TextColumn::make('kode_menu')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('nama_menu')
                ->label('Nama Menu')
                ->searchable()
                ->sortable(),

                TextColumn::make('kategori'),

                TextColumn::make('harga')
                ->money('IDR', locale: 'id'),

                TextColumn::make('hpp')
                ->label('HPP')
                ->money('IDR', locale: 'id'),

                TextColumn::make('margin')
                ->money('IDR', locale: 'id')
                ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),

                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean()
                    ->state(fn ($record): bool => $record->status === 'tersedia'),

                    TextColumn::make('status_stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                    'Tersedia' => 'success',
                    'Habis' => 'danger',
                    default => 'gray',
    }),

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
