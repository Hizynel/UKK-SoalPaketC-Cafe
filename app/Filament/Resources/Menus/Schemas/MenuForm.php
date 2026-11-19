<?php

namespace App\Filament\Resources\Menus\Schemas;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
        TextInput::make('kode_menu')
            ->label('Kode Menu')
            ->placeholder('Contoh: MNU-001')
            ->unique(ignoreRecord: true),

        TextInput::make('nama_menu')
        ->required(),

        Select::make('kategori')
            ->options([
                'Minuman' => 'Minuman',
                'Makanan' => 'Makanan',
            ])
            ->required(),

        TextInput::make('harga')
        ->numeric()
        ->minValue(0)
        ->prefix('Rp')
        ->required(),

        Toggle::make('status')
        ->label('Aktif')
        ->formatStateUsing(fn ($state): bool => $state === 'tersedia')
        ->dehydrateStateUsing(fn (bool $state): string => $state ? 'tersedia' : 'habis')
        ->default(true),

        Repeater::make('reseps')
            ->relationship()
            ->label('Resep')
            ->schema([
                Select::make('id_bahan_baku')
                    ->label('Bahan')
                    ->relationship('bahanBaku', 'nama_bahan')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->distinct(),
                TextInput::make('takaran')
                ->numeric()
                ->minValue(0.01)
                ->required(),
            ])
            ->columns(2)
            ->columnSpanFull(),
    ]);
    }
}
