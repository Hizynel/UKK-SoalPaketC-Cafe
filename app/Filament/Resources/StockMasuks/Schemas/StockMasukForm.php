<?php

namespace App\Filament\Resources\StockMasuks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class StockMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Select::make('id_bahan_baku')
                ->label('Bahan')
                ->relationship('bahanBaku', 'nama_bahan')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('jumlah')
                ->numeric()
                ->minValue(0.01)
                ->required(),
            DateTimePicker::make('tanggal_pembelian')
            ->default(now())
            ->required(),
        ]);
    }
}
