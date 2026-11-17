<?php

namespace App\Filament\Pages;

use App\Models\bahan_baku;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class LaporanBahanMenipis extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-bahan-menipis';
    protected static ?string $navigationLabel = 'Bahan Menipis';
    protected static ?string $title = 'Laporan Bahan Menipis';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    public function table(Table $table): Table
    {
        return $table
            ->query(bahan_baku::query()->whereColumn('stock', '<=', 'batas_minimun'))
            ->columns([
                TextColumn::make('nama_bahan')->label('Bahan')->searchable(),
                TextColumn::make('satuan'),
                TextColumn::make('stock')->label('Stok')->color('danger'),
                TextColumn::make('batas_minimun')->label('Batas minimum'),
            ])
            ->emptyStateHeading('Tidak ada bahan yang menipis');
    }
}