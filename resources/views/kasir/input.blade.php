@extends('layouts.kasir')
@section('title', 'Masukan Pesanan')
@section('content')
<form class="grid grid-cols-3 gap-6" onsubmit="return false">
    <div class="col-span-2 rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-sm font-semibold">Nama Pelanggan</label>
                <input type="text" class="w-full rounded-xl border border-[#d9cdbf] px-4 py-3 outline-none focus:border-[#6f4e37]">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Nomor Meja (1–10)</label>
                <select class="w-full rounded-xl border border-[#d9cdbf] bg-white px-4 py-3">
                    @for ($i = 1; $i <= 10; $i++)<option value="{{ $i }}">Meja {{ $i }}</option>@endfor
                </select>
            </div>
        </div>

        <label class="mb-1 mt-5 block text-sm font-semibold">Metode Pembayaran</label>
        <div class="grid grid-cols-2 gap-1 rounded-xl bg-[#f3e6d6] p-1">
            @foreach (['Tunai', 'QRIS'] as $m)
                <label class="cursor-pointer">
                    <input type="radio" name="metode" value="{{ $m }}" class="peer hidden" {{ $loop->first ? 'checked' : '' }}>
                    <span class="block rounded-lg py-3 text-center font-semibold peer-checked:bg-[#6f4e37] peer-checked:text-white">{{ $m }}</span>
                </label>
            @endforeach
        </div>

        <label class="mb-1 mt-5 block text-sm font-semibold">Item Pesanan</label>
        <div id="items" class="space-y-3"></div>
        <button type="button" id="tambah" class="mt-3 rounded-xl bg-[#f3e6d6] px-4 py-3 font-semibold">+ Tambah Item</button>
    </div>

    <div>
        <div class="rounded-2xl border border-[#eadfd0] bg-white p-6">
            <div class="text-lg font-bold">Rincian Harga</div>
            <div class="mt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span id="subtotal">Rp 0</span></div>
                <div class="flex justify-between"><span>Service charge (5%)</span><span id="service">Rp 0</span></div>
                <div class="flex justify-between"><span>Pajak (10%)</span><span id="pajak">Rp 0</span></div>
            </div>
            <div class="mt-3 flex justify-between border-t border-dashed border-[#d9cdbf] pt-3 text-lg font-bold">
                <span>Total Akhir</span><span id="total">Rp 0</span>
            </div>
        </div>
        <div class="mt-4 rounded-xl bg-[#dcf2e2] px-4 py-3 text-sm font-semibold text-[#1f6b3a]">
            Stok bahan mencukupi untuk seluruh item.
        </div>
        <button type="button" id="simpan" class="mt-4 w-full rounded-xl bg-[#6f4e37] py-4 text-lg font-bold text-white hover:opacity-90">Simpan Pesanan</button>
    </div>
</form>

<div id="modal-sukses" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/45">
    <div class="w-[440px] rounded-2xl bg-white p-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#dcf2e2] text-3xl text-[#1f6b3a]">✓</div>
        <div class="mt-4 text-xl font-bold">Pesanan berhasil disimpan</div>
        <p class="mt-2 text-sm text-[#6b5645]">
            Pesanan atas nama <span id="m-nama" class="font-semibold"></span> masuk ke antrean.
        </p>
        <div id="m-kode" class="my-4 rounded-xl bg-[#f3e6d6] py-4 text-2xl font-extrabold tracking-[0.3em]"></div>
        <div class="mb-5 text-sm text-[#6b5645]">Total <b id="m-total"></b></div>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('kasir.dashboard') }}"
               class="rounded-xl border border-[#6f4e37] py-3 font-bold text-[#6f4e37]">Kembali ke Dashboard</a>
            <button type="button" id="m-baru" class="rounded-xl bg-[#6f4e37] py-3 font-bold text-white">Buat Pesanan Baru</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const MENU = [
        {id: 1, nama: 'Latte', harga: 28000},
        {id: 2, nama: 'Americano', harga: 22000},
        {id: 3, nama: 'Cappuccino', harga: 28000},
        {id: 4, nama: 'Roti Bakar Cokelat', harga: 20000},
        {id: 5, nama: 'Kentang Goreng', harga: 25000},
    ];
    const rp = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const items = document.getElementById('items');

    function tambahBaris() {
        const row = document.createElement('div');
        row.className = 'flex gap-3';
        row.innerHTML = `
            <select class="menu flex-1 rounded-xl border border-[#d9cdbf] bg-white px-4 py-3">
                ${MENU.map(m => `<option value="${m.id}" data-harga="${m.harga}">${m.nama} — ${rp(m.harga)}</option>`).join('')}
            </select>
            <input type="number" min="1" value="1" class="qty w-24 rounded-xl border border-[#d9cdbf] px-3 py-3 text-center">
            <button type="button" class="hapus rounded-xl border border-[#d9cdbf] px-4 text-[#9b2c1f]">✕</button>`;
        items.appendChild(row);
        hitung();
    }

    function hitung() {
        let subtotal = 0;
        items.querySelectorAll('div.flex').forEach(row => {
            const harga = +row.querySelector('.menu').selectedOptions[0].dataset.harga;
            subtotal += harga * (+row.querySelector('.qty').value || 0);
        });
        const service = subtotal * 0.05;
        const pajak = (subtotal + service) * 0.10;
        document.getElementById('subtotal').textContent = rp(subtotal);
        document.getElementById('service').textContent = rp(service);
        document.getElementById('pajak').textContent = rp(pajak);
        document.getElementById('total').textContent = rp(subtotal + service + pajak);
    }

    document.getElementById('tambah').onclick = tambahBaris;
    items.addEventListener('input', hitung);
    items.addEventListener('click', e => {
        if (e.target.classList.contains('hapus')) { e.target.closest('div.flex').remove(); hitung(); }
    });
    const modal = document.getElementById('modal-sukses');
const buka = () => { modal.classList.remove('hidden'); modal.classList.add('flex'); };
const tutup = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };

document.getElementById('simpan').onclick = () => {
    const nama = document.querySelector('input[type=text]').value.trim();
    if (!nama) { alert('Nama pelanggan wajib diisi.'); return; }
    const kode = Math.random().toString(36).slice(2, 10).toUpperCase();
    document.getElementById('m-nama').textContent = nama;
    document.getElementById('m-kode').textContent = kode;
    document.getElementById('m-total').textContent = document.getElementById('total').textContent;
    buka();
};
document.getElementById('m-baru').onclick = () => {
    document.querySelector('input[type=text]').value = '';
    document.querySelector('select:not(.menu)').selectedIndex = 0;
    document.querySelector('input[name=metode][value=Tunai]').checked = true;
    items.innerHTML = '';
    tambahBaris();
    tutup();
};
    tambahBaris();
</script>
@endpush