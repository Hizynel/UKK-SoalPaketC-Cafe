<?php

namespace App\Filament\Pages;

use App\Models\bahan_baku;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LaporanStockHampirHabis extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-stock-hampir-habis';
    protected static ?string $navigationLabel = 'Stock Hampir Habis';
    protected static ?string $title = 'Laporan Stock Hampir Habis';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                bahan_baku::query()
                    ->whereColumn('stock', '<=', 'batas_minimun')
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                TextColumn::make('nama_bahan')
                    ->label('Nama Bahan Baku')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('stock')
                    ->label('Sisa Stok Saat Ini')
                    ->formatStateUsing(fn ($state, bahan_baku $record) => rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.') . " {$record->satuan}")
                    ->color('danger')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('batas_minimun')
                    ->label('Batas Minimum')
                    ->formatStateUsing(fn ($state, bahan_baku $record) => rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.') . " {$record->satuan}")
                    ->sortable(),

                TextColumn::make('status_stok')
                    ->label('Kondisi Stok')
                    ->badge()
                    ->state(fn (bahan_baku $record) => (float) $record->stock <= 0 ? 'Habis (0)' : 'Menipis')
                    ->color(fn ($state) => match ($state) {
                        'Habis (0)' => 'danger',
                        'Menipis' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('kekurangan')
                    ->label('Perlu Ditambah (Min)')
                    ->state(fn (bahan_baku $record) => max(0, (float) $record->batas_minimun - (float) $record->stock))
                    ->formatStateUsing(fn ($state, bahan_baku $record) => rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.') . " {$record->satuan}")
                    ->color('primary'),

                TextColumn::make('harga_terakhir')
                    ->label('Harga per Satuan')
                    ->money('IDR', locale: 'id')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kondisi')
                    ->label('Kondisi')
                    ->options([
                        'habis' => 'Habis (Stok 0)',
                        'menipis' => 'Menipis (Stok > 0)',
                    ])
                    ->query(function ($query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'habis') {
                            $query->where('stock', '<=', 0);
                        } elseif ($val === 'menipis') {
                            $query->where('stock', '>', 0);
                        }
                    }),
            ])
            ->recordActions([
                Action::make('tambahStok')
                    ->label('Input Stok')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->url(fn (bahan_baku $record) => route('filament.manager.resources.stock-masuks.create')),
            ])
            ->emptyStateHeading('Semua Stok Bahan Baku Aman')
            ->emptyStateDescription('Tidak ada bahan baku yang berada di bawah atau sama dengan batas minimum stok.');
    }
}

