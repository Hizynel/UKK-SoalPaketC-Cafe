<?php

namespace App\Filament\Pages;

use App\Models\pesanan;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LaporanRiwayatPesanan extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.laporan-riwayat-pesanan';
    protected static ?string $navigationLabel = 'Riwayat Pesanan';
    protected static ?string $title = 'Laporan Riwayat Pesanan';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                pesanan::query()
                    ->with(['kasir', 'detailPesanan'])
                    ->latest('tanggal_pesanan')
            )
            ->columns([
                TextColumn::make('kode_unik')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('tanggal_pesanan')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nomor_meja')
                    ->label('Meja')
                    ->formatStateUsing(fn ($state) => "Meja {$state}")
                    ->sortable(),

                TextColumn::make('items_summary')
                    ->label('Menu Dipesan')
                    ->state(fn (pesanan $record) => $record->detailPesanan->map(fn ($d) => "{$d->nama_menu} ({$d->jumlah})")->implode(', '))
                    ->limit(35)
                    ->tooltip(fn (pesanan $record) => $record->detailPesanan->map(fn ($d) => "{$d->nama_menu} ({$d->jumlah})")->implode(', ')),

                TextColumn::make('total_harga')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('metode_pesan')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'tunai' => 'success',
                        'qris' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status_pesanan')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'baru' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('kasir.name')
                    ->label('Kasir')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->options([
                        'baru' => 'Baru',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),

                SelectFilter::make('metode_pesan')
                    ->label('Metode Pembayaran')
                    ->options([
                        'tunai' => 'Tunai',
                        'qris' => 'QRIS',
                    ]),

                Filter::make('periode')
                    ->schema([
                        DatePicker::make('dari')->label('Dari Tanggal'),
                        DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['dari'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_pesanan', '>=', $v))
                        ->when($data['sampai'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_pesanan', '<=', $v))
                    ),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Rincian')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (pesanan $record) => "Rincian Pesanan #{$record->kode_unik}")
                    ->modalContent(fn (pesanan $record) => view('filament.pages.actions.detail-pesanan-modal', [
                        'pesanan' => $record->loadMissing(['kasir', 'detailPesanan']),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->emptyStateHeading('Belum ada riwayat pesanan')
            ->emptyStateDescription('Semua transaksi pesanan yang dibuat oleh kasir akan tercatat di sini.');
    }
}

