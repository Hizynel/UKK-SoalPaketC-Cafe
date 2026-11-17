<?php

namespace App\Filament\Pages;

use App\Models\menu;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class LaporanPenjualan extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-penjualan';
    protected static ?string $navigationLabel = 'Penjualan';
    protected static ?string $title = 'Laporan Penjualan per Menu';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                menu::query()
                    ->join('detail_pesanans as d', 'd.id_menu', '=', 'menus.id')
                    ->join('pesanans as p', 'p.id', '=', 'd.id_pesanan')
                    ->where('p.status_pesanan', '!=', 'dibatalkan')
                    ->groupBy('menus.id', 'menus.nama_menu')
                    ->select('menus.id', 'menus.nama_menu')
                    ->selectRaw('SUM(d.jumlah) as total_porsi')
                    ->selectRaw('SUM(d.subtotal) as total_pendapatan')
            )
            ->columns([
                TextColumn::make('nama_menu')->label('Menu'),
                TextColumn::make('total_porsi')->label('Porsi terjual')->sortable(),
                TextColumn::make('total_pendapatan')->label('Pendapatan')
                    ->money('IDR', locale: 'id')->sortable(),
            ])
            ->filters([
                Filter::make('periode')
                    ->schema([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['dari'] ?? null, fn ($q, $v) => $q->whereDate('p.tanggal_pesanan', '>=', $v))
                        ->when($data['sampai'] ?? null, fn ($q, $v) => $q->whereDate('p.tanggal_pesanan', '<=', $v))
                    ),
            ])
            ->defaultSort('total_porsi', 'desc');
    }
}