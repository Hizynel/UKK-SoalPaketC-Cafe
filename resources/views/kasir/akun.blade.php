@extends('layouts.kasir')
@section('title', 'Manajemen Akun Pelanggan')
@section('content')
@php
    $pelanggan = [
        ['Rina Wulandari', '081234567890', '02 Nov 2026'],
        ['Dimas Pratama', '085711223344', '03 Nov 2026'],
        ['Sari Anggraini', '082198765432', '05 Nov 2026'],
        ['Budi Santoso', '081377889900', '06 Nov 2026'],
        ['Ayu Lestari', '087855443322', '08 Nov 2026'],
    ];
@endphp
<input type="text" placeholder="Cari nama atau nomor HP"
       class="mb-5 w-80 rounded-xl border border-[#d9cdbf] bg-white px-4 py-3 outline-none focus:border-[#6f4e37]">

<div class="overflow-hidden rounded-2xl border border-[#eadfd0] bg-white">
    <table class="w-full text-left">
        <thead class="bg-[#f3e6d6] text-sm text-[#6b5645]">
            <tr><th class="px-6 py-3">Nama</th><th class="px-6 py-3">Nomor HP</th><th class="px-6 py-3">Terdaftar</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($pelanggan as [$nama, $hp, $tgl])
            <tr class="border-t border-[#eadfd0]">
                <td class="px-6 py-4 font-bold">{{ $nama }}</td>
                <td class="px-6 py-4">{{ $hp }}</td>
                <td class="px-6 py-4">{{ $tgl }}</td>
                <td class="px-6 py-4 text-right">
                    <button class="reset rounded-lg bg-[#7a5416] px-4 py-2 font-semibold text-white" data-nama="{{ $nama }}">Reset Password</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div id="modal" class="fixed inset-0 hidden items-center justify-center bg-black/45">
    <div class="w-[440px] rounded-2xl bg-white p-8">
        <div class="text-lg font-bold">Password sementara dibuat</div>
        <p class="mt-2 text-sm text-[#6b5645]">
            Berikan ke <span id="nama" class="font-semibold"></span>. Pelanggan wajib menggantinya saat login berikutnya.
        </p>
        <div id="kode" class="my-4 rounded-xl bg-[#f3e6d6] py-4 text-center text-3xl font-extrabold tracking-[0.3em]"></div>
        <button id="selesai" class="w-full rounded-xl bg-[#6f4e37] py-3 font-bold text-white">Selesai</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modal');
    document.querySelectorAll('.reset').forEach(b => b.onclick = () => {
        document.getElementById('nama').textContent = b.dataset.nama;
        document.getElementById('kode').textContent = Math.random().toString(36).slice(2, 10).toUpperCase();
        modal.classList.remove('hidden'); modal.classList.add('flex');
    });
    document.getElementById('selesai').onclick = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
</script>
@endpush