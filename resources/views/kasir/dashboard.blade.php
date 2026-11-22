@extends('layouts.kasir')
@section('title', 'Dashboard')
@section('content')
@if (session('error') || session('success'))
    <div class="mb-5 rounded-xl {{ session('error') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} px-4 py-3">
        {{ session('error') ?? session('success') }}
    </div>
@endif
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Pesanan Baru</div>
        <div class="mt-1 text-4xl font-bold text-[#8a5a00]">{{ $baruCount }}</div>
    </div>
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Sedang Diproses</div>
        <div class="mt-1 text-4xl font-bold text-[#1d4e89]">{{ $diprosesCount }}</div>
    </div>
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Siap</div>
        <div class="mt-1 text-4xl font-bold text-purple-700">{{ $siapCount }}</div>
    </div>
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Selesai Hari Ini</div>
        <div class="mt-1 text-4xl font-bold text-[#1f6b3a]">{{ $selesaiHariIniCount }}</div>
    </div>
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-[#eadfd0] bg-white">
    <div class="px-6 py-5 text-lg font-bold">Antrean Pesanan Baru</div>
    <table class="w-full text-left">
        <thead class="bg-[#f3e6d6] text-sm text-[#6b5645]">
            <tr>
                <th class="px-6 py-3">Kode</th><th class="px-6 py-3">Pelanggan</th>
                <th class="px-6 py-3">Meja</th><th class="px-6 py-3">Total</th>
                <th class="px-6 py-3">Masuk</th><th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($antrean as $pesanan)
            <tr class="border-t border-[#eadfd0]">
                <td class="px-6 py-4 font-bold">{{ $pesanan->kode_unik }}</td>
                <td class="px-6 py-4">{{ $pesanan->nama_pelanggan }}</td>
                <td class="px-6 py-4">Meja {{ $pesanan->nomor_meja }}</td>
                <td class="px-6 py-4">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                <td class="px-6 py-4">{{ $pesanan->tanggal_pesanan->diffForHumans() }}</td>
                <td class="px-6 py-4 text-right">
                    <form method="POST" action="{{ route('kasir.proses', $pesanan) }}" class="inline">
                        @csrf
                        <button class="rounded-lg bg-[#1d4e89] px-4 py-2 font-semibold text-white">Proses</button>
                    </form>
                    <form method="POST" action="{{ route('kasir.batal', $pesanan) }}" class="ml-1 inline">
                        @csrf
                        <button class="rounded-lg border border-[#9b2c1f] px-4 py-2 font-semibold text-[#9b2c1f]">Batalkan</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-[#6b5645]">Belum ada pesanan baru.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection