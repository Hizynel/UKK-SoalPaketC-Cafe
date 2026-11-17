@extends('layouts.kasir')
@section('title', 'Dashboard')
@section('content')
@php
    $antrean = [
        ['K7M2QX4A', 'Rina Wulandari', 3, 54450, '2 menit lalu'],
        ['P9TR5WNB', 'Dimas Pratama', 7, 38500, '5 menit lalu'],
        ['H4XZ8C2D', 'Sari Anggraini', 1, 71500, '8 menit lalu'],
        ['M3N6V9LE', 'Budi Santoso', 5, 27500, '11 menit lalu'],
    ];
@endphp
<div class="grid grid-cols-3 gap-5">
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Pesanan Baru</div>
        <div class="mt-1 text-4xl font-bold text-[#8a5a00]">4</div>
    </div>
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Sedang Diproses</div>
        <div class="mt-1 text-4xl font-bold text-[#1d4e89]">2</div>
    </div>
    <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="text-sm text-[#6b5645]">Selesai Hari Ini</div>
        <div class="mt-1 text-4xl font-bold text-[#1f6b3a]">17</div>
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
        @foreach ($antrean as [$kode, $nama, $meja, $total, $masuk])
            <tr class="border-t border-[#eadfd0]">
                <td class="px-6 py-4 font-bold">{{ $kode }}</td>
                <td class="px-6 py-4">{{ $nama }}</td>
                <td class="px-6 py-4">Meja {{ $meja }}</td>
                <td class="px-6 py-4">Rp {{ number_format($total, 0, ',', '.') }}</td>
                <td class="px-6 py-4">{{ $masuk }}</td>
                <td class="px-6 py-4 text-right">
                    <button class="rounded-lg bg-[#1d4e89] px-4 py-2 font-semibold text-white">Proses</button>
                    <button class="ml-1 rounded-lg border border-[#9b2c1f] px-4 py-2 font-semibold text-[#9b2c1f]">Batalkan</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection