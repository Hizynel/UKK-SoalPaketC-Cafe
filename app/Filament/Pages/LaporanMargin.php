<?php

namespace App\Filament\Pages;

use App\Models\menu;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class LaporanMargin extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-margin';
    protected static ?string $navigationLabel = 'Margin Menu';
    protected static ?string $title = 'Laporan Margin per Menu';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(menu::query()->with('reseps.bahanBaku'))
            ->columns([
                TextColumn::make('nama_menu')->label('Menu')->searchable(),
                TextColumn::make('harga')->label('Harga jual')->money('IDR', locale: 'id'),
                TextColumn::make('hpp')->label('HPP')->money('IDR', locale: 'id'),
                TextColumn::make('margin')->money('IDR', locale: 'id')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
            ]);
    }
}