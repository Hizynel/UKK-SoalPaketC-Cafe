<?php

namespace App\Filament\Resources\Reseps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_menu')
                ->label('Menu')
                ->relationship('menu', 'nama_menu')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('id_bahan_baku')
                ->label('Bahan Baku')
                ->relationship('bahanBaku', 'nama_bahan')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('takaran')
                ->numeric()
                ->minValue(0.01)
                ->required(),
        ]);
    }
}
