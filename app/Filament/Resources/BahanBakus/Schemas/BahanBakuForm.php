<?php

namespace App\Filament\Resources\BahanBakus\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\validation\Rules\Unique;

class BahanBakuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
        TextInput::make('kode_bahan')
            ->label('Kode Bahan')
            ->placeholder('Contoh: BHN-001')
            ->unique(ignoreRecord: true),

        TextInput::make('nama_bahan')->required()->dehydrateStateUsing(fn ($state) => trim($state))->unique(
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule) => $rule,
            ),
        Select::make('satuan')
        ->options(['g' => 'g', 'ml' => 'ml', 'pcs' => 'pcs'])
        ->required(),
        
        TextInput::make('stock')
        ->numeric()
        ->minValue(0)
        ->default(0)
        ->required(),

        TextInput::make('batas_minimun')
        ->label('Batas minimum')
        ->numeric()
        ->minValue(0)
        ->default(0)
        ->required(),

        TextInput::make('harga_terakhir')
            ->numeric()
            ->minValue(0)
            ->label('Harga per satuan dasar'),
    ]);
    }
}
