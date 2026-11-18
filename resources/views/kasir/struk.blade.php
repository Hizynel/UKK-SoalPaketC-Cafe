@extends('layouts.kasir')
@section('title', 'Struk Pesanan')
@section('content')
<div class="mx-auto max-w-xl rounded-2xl border border-[#eadfd0] bg-white p-8">
    <div class="text-center text-lg font-bold">Kedai Kopi Senja</div>
    <div class="text-center text-sm text-[#6b5645]">Struk Pesanan {{ $pesanan->kode_unik }}</div>
    <div class="mt-2 text-center text-sm text-[#6b5645]">
        {{ $pesanan->tanggal_pesanan->format('d/m/Y H:i') }} · Meja {{ $pesanan->nomor_meja }}
    </div>
    <div class="mt-6 space-y-2 text-sm">
        @foreach ($pesanan->detailPesanan as $detail)
            <div class="flex justify-between">
                <span>{{ $detail->jumlah }}× {{ $detail->nama_menu ?? $detail->menu?->nama_menu ?? 'Menu tidak tersedia' }}</span>
                <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
        <div class="flex justify-between border-t border-dashed pt-2"><span>Subtotal</span><span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>Service 5%</span><span>Rp {{ number_format($pesanan->service, 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>Pajak 10%</span><span>Rp {{ number_format($pesanan->pajak, 0, ',', '.') }}</span></div>
        <div class="flex justify-between border-t border-dashed pt-2 text-base font-bold"><span>Total</span><span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span></div>
    </div>
    <a href="{{ route('kasir.data') }}" class="mt-6 block rounded-xl bg-[#6f4e37] py-3 text-center font-bold text-white">Kembali ke Data Pesanan</a>
</div>
@endsection
