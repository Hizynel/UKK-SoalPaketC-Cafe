@extends('layouts.kasir')
@section('title', 'Data Pesanan / Riwayat')
@section('content')
@php
    $pesanan = [
        ['K7M2QX4A', 'Rina Wulandari', 3, 73920, 'Baru'],
        ['P9TR5WNB', 'Dimas Pratama', 7, 38500, 'Diproses'],
        ['H4XZ8C2D', 'Sari Anggraini', 1, 71500, 'Selesai'],
        ['M3N6V9LE', 'Budi Santoso', 5, 27500, 'Baru'],
        ['T8B2K5QY', 'Ayu Lestari', 2, 44000, 'Selesai'],
        ['C5W7R2HJ', 'Eko Prasetyo', 9, 33000, 'Diproses'],
    ];
    $badge = [
        'Baru' => 'bg-[#fdf0d0] text-[#8a5a00]',
        'Diproses' => 'bg-[#dce8fb] text-[#1d4e89]',
        'Selesai' => 'bg-[#dcf2e2] text-[#1f6b3a]',
    ];
@endphp
<div class="mb-5 flex gap-2" id="tabs">
    @foreach (['Semua', 'Baru', 'Diproses', 'Selesai'] as $t)
        <button data-filter="{{ $t }}"
                class="tab rounded-full border border-[#d9cdbf] px-4 py-2 font-semibold {{ $loop->first ? 'bg-[#6f4e37] text-white' : 'bg-white' }}">
            {{ $t }} ({{ $t === 'Semua' ? count($pesanan) : collect($pesanan)->where(4, $t)->count() }})
        </button>
    @endforeach
</div>

<div class="overflow-hidden rounded-2xl border border-[#eadfd0] bg-white">
    <table class="w-full text-left">
        <thead class="bg-[#f3e6d6] text-sm text-[#6b5645]">
            <tr><th class="px-6 py-3">Kode</th><th class="px-6 py-3">Pelanggan</th><th class="px-6 py-3">Meja</th>
                <th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($pesanan as [$kode, $nama, $meja, $total, $status])
            <tr class="baris border-t border-[#eadfd0]" data-status="{{ $status }}">
                <td class="px-6 py-4 font-bold">{{ $kode }}</td>
                <td class="px-6 py-4">{{ $nama }}</td>
                <td class="px-6 py-4">Meja {{ $meja }}</td>
                <td class="px-6 py-4">Rp {{ number_format($total, 0, ',', '.') }}</td>
                <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$status] }}">{{ $status }}</span></td>
                <td class="px-6 py-4 text-right">
                    @if ($status === 'Baru')
                        <button class="rounded-lg bg-[#1d4e89] px-4 py-2 font-semibold text-white">Proses</button>
                        <button class="ml-1 rounded-lg border border-[#9b2c1f] px-4 py-2 font-semibold text-[#9b2c1f]">Batalkan</button>
                    @elseif ($status === 'Diproses')
                        <button class="rounded-lg bg-[#1f6b3a] px-4 py-2 font-semibold text-white">Selesaikan</button>
                    @else
                        <button class="btn-struk rounded-lg bg-[#f3e6d6] px-4 py-2 font-semibold">Lihat Struk</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<p class="mt-4 text-sm text-[#6b5645]">
    Status berubah berjenjang: Baru → Diproses → Selesai. Pembatalan hanya untuk pesanan Baru dan stok bahan kembali otomatis.
</p>

<div id="struk" class="fixed inset-0 hidden items-center justify-center bg-black/45">
    <div class="w-[420px] rounded-2xl bg-white p-8">
        <div class="text-center text-lg font-bold">Kedai Kopi Senja</div>
        <div class="text-center text-sm text-[#6b5645]">Struk Pesanan H4XZ8C2D</div>
        <div class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span>2× Latte</span><span>56.000</span></div>
            <div class="flex justify-between"><span>1× Roti Bakar Cokelat</span><span>20.000</span></div>
            <div class="flex justify-between border-t border-dashed pt-1"><span>Subtotal</span><span>76.000</span></div>
            <div class="flex justify-between"><span>Service 5%</span><span>3.800</span></div>
            <div class="flex justify-between"><span>Pajak 10%</span><span>7.980</span></div>
            <div class="flex justify-between border-t border-dashed pt-2 text-base font-bold"><span>Total</span><span>Rp 87.780</span></div>
        </div>
        <button id="tutup" class="mt-6 w-full rounded-xl bg-[#6f4e37] py-3 font-bold text-white">Tutup</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tab').forEach(btn => btn.onclick = () => {
        const f = btn.dataset.filter;
        document.querySelectorAll('.tab').forEach(b => {
            const on = b === btn;
            b.classList.toggle('bg-[#6f4e37]', on); b.classList.toggle('text-white', on);
            b.classList.toggle('bg-white', !on);
        });
        document.querySelectorAll('.baris').forEach(r =>
            r.style.display = (f === 'Semua' || r.dataset.status === f) ? '' : 'none');
    });
    const struk = document.getElementById('struk');
    document.querySelectorAll('.btn-struk').forEach(b => b.onclick = () => { struk.classList.remove('hidden'); struk.classList.add('flex'); });
    document.getElementById('tutup').onclick = () => { struk.classList.add('hidden'); struk.classList.remove('flex'); };
</script>
@endpush