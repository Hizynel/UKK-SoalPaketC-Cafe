<div class="space-y-6">
    <!-- Informasi Utama Pesanan -->
    <div class="grid grid-cols-2 gap-4 rounded-xl bg-gray-50 p-4 dark:bg-white/5">
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kode Pesanan</span>
            <div class="font-bold text-gray-900 dark:text-white">#{{ $pesanan->kode_unik }}</div>
        </div>
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Waktu Transaksi</span>
            <div class="font-semibold text-gray-900 dark:text-white">{{ $pesanan->tanggal_pesanan?->format('d M Y, H:i') ?? '—' }}</div>
        </div>
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Pelanggan</span>
            <div class="font-semibold text-gray-900 dark:text-white">{{ $pesanan->nama_pelanggan }} (Meja {{ $pesanan->nomor_meja }})</div>
        </div>
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kasir</span>
            <div class="font-semibold text-gray-900 dark:text-white">{{ $pesanan->kasir?->name ?? 'Kasir Kafe' }}</div>
        </div>
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Metode Pembayaran</span>
            <div>
                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold {{ strtolower($pesanan->metode_pesan) === 'tunai' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400' }}">
                    {{ strtoupper($pesanan->metode_pesan) }}
                </span>
            </div>
        </div>
        <div>
            <span class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status Pesanan</span>
            <div>
                @php
                    $statusColor = match($pesanan->status_pesanan) {
                        'selesai' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400',
                        'diproses' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400',
                        'baru' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                        'dibatalkan' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400',
                        default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
                    };
                @endphp
                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold {{ $statusColor }}">
                    {{ ucfirst($pesanan->status_pesanan) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Menu Dipesan -->
    <div>
        <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Rincian Menu yang Dipesan</h4>
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:bg-white/5 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2.5">No</th>
                        <th class="px-4 py-2.5">Menu</th>
                        <th class="px-4 py-2.5 text-center">Jumlah</th>
                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse ($pesanan->detailPesanan as $i => $item)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $item->nama_menu }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-900 dark:text-white">{{ $item->jumlah }}</td>
                            <td class="px-4 py-2.5 text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada detail item pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rincian Biaya & Total -->
    <div class="space-y-1.5 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm dark:border-white/10 dark:bg-white/5">
        <div class="flex justify-between text-gray-600 dark:text-gray-300">
            <span>Subtotal Menu</span>
            <span class="font-medium">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
        </div>
        @if ($pesanan->service > 0)
            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                <span>Biaya Layanan (Service)</span>
                <span class="font-medium">Rp {{ number_format($pesanan->service, 0, ',', '.') }}</span>
            </div>
        @endif
        @if ($pesanan->pajak > 0)
            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                <span>Pajak (PB1)</span>
                <span class="font-medium">Rp {{ number_format($pesanan->pajak, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold text-gray-900 dark:border-white/10 dark:text-white">
            <span>Total Bayar</span>
            <span class="text-amber-600 dark:text-amber-400">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
