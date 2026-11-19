@extends('layouts.kasir')
@section('title', 'Menu')

@section('content')
    @if ($menus->isEmpty())
        <div class="rounded-2xl border border-[#eadfd0] bg-white p-12 text-center">
            <h3 class="text-lg font-bold text-[#2a1f18]">Belum Ada Menu</h3>
            <p class="mt-1 text-sm text-[#6b5645]">Menu akan tampil di sini setelah ditambahkan oleh manajer.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-[#eadfd0] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#faf6f0] text-sm text-[#6b5645]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama Menu</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 text-right font-semibold">Harga</th>
                            <th class="px-6 py-4 text-center font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eadfd0]">
                        @foreach ($menus as $menu)
                            @php($statusStok = $menu->status_stok)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-[#2a1f18]">{{ $menu->nama_menu }}</td>
                                <td class="px-6 py-4 text-[#6b5645]">{{ $menu->kategori }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-[#2a1f18]">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span @class([
                                        'rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-800' => $statusStok === 'Tersedia',
                                        'bg-red-100 text-red-800' => $statusStok === 'Habis',
                                        'bg-gray-100 text-gray-600' => $statusStok === 'Tanpa resep',
                                    ])>
                                        {{ $statusStok }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
