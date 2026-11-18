@extends('layouts.kasir')
@section('title', 'Daftar Resep Menu')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <!-- Form Pencarian & Filter -->
    <form method="GET" action="{{ route('kasir.resep') }}" class="flex flex-wrap items-center gap-2">
        <input type="search" name="q" id="search-input" value="{{ $q }}"
               placeholder="Cari menu atau nama bahan..."
               class="w-80 rounded-xl border border-[#d9cdbf] bg-white px-4 py-3 outline-none focus:border-[#6f4e37]">
        
        <input type="hidden" name="kategori" id="kategori-input" value="{{ $kategori }}">
        <button type="submit" class="rounded-xl bg-[#6f4e37] px-5 py-3 font-semibold text-white">Cari</button>

        @if ($q || $kategori !== 'semua')
            <a href="{{ route('kasir.resep') }}" class="rounded-xl border border-[#d9cdbf] bg-white px-5 py-3 font-semibold text-[#6b5645] hover:bg-[#f3e6d6]">
                Reset
            </a>
        @endif
    </form>

    <!-- Label Read-Only Mode -->
    <div class="flex items-center gap-2 rounded-xl border border-[#eadfd0] bg-white px-4 py-2.5 text-sm font-medium text-[#6b5645]">
        <svg class="h-5 w-5 text-[#6f4e37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <span>Mode Hanya Lihat (Read-Only)</span>
    </div>
</div>

<!-- Filter Kategori Tabs -->
<div class="mb-6 flex flex-wrap gap-2" id="kategori-tabs">
    @php
        $semuaKategori = array_merge(['semua' => 'Semua Kategori'], array_combine($daftarKategori, $daftarKategori));
    @endphp
    @foreach ($semuaKategori as $val => $label)
        <a href="{{ route('kasir.resep', array_filter(['q' => $q, 'kategori' => $val === 'semua' ? null : $val])) }}"
           data-kategori="{{ strtolower($val) }}"
           class="tab-btn rounded-full border border-[#d9cdbf] px-4 py-2 font-semibold transition {{ ($kategori === $val || ($val === 'semua' && $kategori === 'semua')) ? 'bg-[#6f4e37] text-white' : 'bg-white text-[#6b5645] hover:bg-[#f3e6d6]' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<!-- Daftar Menu & Resep -->
@if ($menus->isEmpty())
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-12 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#f3e6d6] text-2xl text-[#6f4e37]">
            📖
        </div>
        <h3 class="mt-4 text-lg font-bold text-[#2a1f18]">Tidak Ada Resep Ditemukan</h3>
        <p class="mt-1 text-sm text-[#6b5645]">
            @if ($q || $kategori !== 'semua')
                Tidak ada menu atau resep yang cocok dengan kriteria pencarian Anda.
            @else
                Belum ada data menu dan resep yang tersedia saat ini.
            @endif
        </p>
        @if ($q || $kategori !== 'semua')
            <a href="{{ route('kasir.resep') }}" class="mt-4 inline-block rounded-xl bg-[#6f4e37] px-5 py-2.5 font-semibold text-white">
                Tampilkan Semua Resep
            </a>
        @endif
    </div>
@else
    <div class="space-y-5" id="menu-list">
        @foreach ($menus as $menu)
            @php
                $statusStok = $menu->status_stok;
                $badgeStatus = [
                    'Tersedia' => 'bg-[#dcf2e2] text-[#1f6b3a]',
                    'Habis' => 'bg-red-100 text-red-800',
                    'Tanpa resep' => 'bg-gray-100 text-gray-600',
                ];
                $badgeKategori = match ($menu->kategori) {
                    'Kopi' => 'bg-[#eedcc8] text-[#5c3d22]',
                    'Non-Kopi' => 'bg-[#dce8fb] text-[#1d4e89]',
                    'Makanan' => 'bg-[#fef3c7] text-[#92400e]',
                    default => 'bg-[#f3e6d6] text-[#6b5645]',
                };
            @endphp
            <div class="menu-card overflow-hidden rounded-2xl border border-[#eadfd0] bg-white shadow-sm"
                 data-nama="{{ strtolower($menu->nama_menu) }}"
                 data-kategori="{{ strtolower($menu->kategori) }}"
                 data-bahan="{{ strtolower($menu->reseps->map(fn ($r) => $r->bahanBaku?->nama_bahan ?? '')->implode(' ')) }}">
                
                <!-- Menu Header -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eadfd0] bg-[#faf6f0] px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-[#2a1f18]">{{ $menu->nama_menu }}</span>
                        <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ $badgeKategori }}">
                            {{ $menu->kategori }}
                        </span>
                        <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ $badgeStatus[$statusStok] ?? 'bg-gray-100 text-gray-600' }}">
                            Stok: {{ $statusStok }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-[#6b5645]">
                            Harga Jual: <span class="font-bold text-[#2a1f18]">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                        </span>
                    </div>
                </div>

                <!-- Resep Content -->
                <div class="p-6">
                    @if ($menu->reseps->isEmpty())
                        <div class="rounded-xl bg-[#faf5ee] p-4 text-center text-sm text-[#6b5645]">
                            <em>Belum ada komposisi resep yang ditentukan untuk menu ini.</em>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-[#eadfd0] text-xs font-semibold uppercase text-[#6b5645]">
                                    <tr>
                                        <th class="pb-3 pr-4">No</th>
                                        <th class="pb-3 pr-4">Nama Bahan Baku</th>
                                        <th class="pb-3 pr-4">Takaran per Porsi</th>
                                        <th class="pb-3 pr-4">Sisa Stok di Kafe</th>
                                        <th class="pb-3 text-right">Ketersediaan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f3e6d6]">
                                    @foreach ($menu->reseps as $index => $resep)
                                        @php
                                            $bahan = $resep->bahanBaku;
                                            $stokCukup = $bahan && ($bahan->stock >= $resep->takaran);
                                        @endphp
                                        <tr class="hover:bg-[#faf5ee]/50">
                                            <td class="py-3 pr-4 text-[#6b5645]">{{ $index + 1 }}</td>
                                            <td class="py-3 pr-4 font-semibold text-[#2a1f18]">
                                                {{ $bahan?->nama_bahan ?? 'Bahan tidak ditemukan' }}
                                            </td>
                                            <td class="py-3 pr-4 font-medium text-[#6f4e37]">
                                                {{ rtrim(rtrim(number_format($resep->takaran, 2, '.', ''), '0'), '.') }} {{ $bahan?->satuan ?? '' }}
                                            </td>
                                            <td class="py-3 pr-4 text-[#6b5645]">
                                                @if ($bahan)
                                                    {{ rtrim(rtrim(number_format($bahan->stock, 2, '.', ''), '0'), '.') }} {{ $bahan->satuan }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                @if (! $bahan)
                                                    <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Tidak Ada</span>
                                                @elseif ($stokCukup)
                                                    <span class="rounded-md bg-[#dcf2e2] px-2.5 py-1 text-xs font-semibold text-[#1f6b3a]">Cukup</span>
                                                @else
                                                    <span class="rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Habis</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<p class="mt-6 text-xs text-[#8c7460]">
    Catatan: Halaman ini bersifat hanya lihat (read-only) sebagai referensi takaran resep bagi kasir. Pembuatan, pengubahan, atau penghapusan resep dikelola secara penuh oleh Manajer.
</p>
@endsection

@push('scripts')
<script>
    // Live client-side search filtering
    const searchInput = document.getElementById('search-input');
    const menuCards = document.querySelectorAll('.menu-card');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            menuCards.forEach(card => {
                const nama = card.dataset.nama || '';
                const bahan = card.dataset.bahan || '';
                if (nama.includes(query) || bahan.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
</script>
@endpush

