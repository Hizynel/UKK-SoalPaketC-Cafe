@extends('layouts.kasir')
@section('title', 'Masukan Pesanan')
@section('content')
@if ($errors->any())
    <div class="mb-5 rounded-xl bg-red-100 px-4 py-3 text-red-800">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('kasir.input.store') }}" class="grid grid-cols-3 gap-6">
    @csrf
    <div class="col-span-2 rounded-2xl border border-[#eadfd0] bg-white p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="nama_pelanggan" class="mb-1 block text-sm font-semibold">Nama Pelanggan</label>
                <input id="nama_pelanggan" name="nama_pelanggan" type="text" value="{{ old('nama_pelanggan') }}" required
                       class="w-full rounded-xl border border-[#d9cdbf] px-4 py-3 outline-none focus:border-[#6f4e37]">
            </div>
            <div>
                <label for="nomor_meja" class="mb-1 block text-sm font-semibold">Nomor Meja (1–10)</label>
                <select id="nomor_meja" name="nomor_meja" required
                        class="w-full rounded-xl border border-[#d9cdbf] bg-white px-4 py-3">
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" @selected(old('nomor_meja', 1) == $i)>Meja {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <label class="mb-1 mt-5 block text-sm font-semibold">Metode Pembayaran</label>
        <div class="grid grid-cols-2 gap-1 rounded-xl bg-[#f3e6d6] p-1">
            @foreach (['Tunai', 'QRIS'] as $method)
                <label class="cursor-pointer">
                    <input type="radio" name="metode" value="{{ $method }}" class="peer hidden"
                           @checked(old('metode', 'Tunai') === $method)>
                    <span class="block rounded-lg py-3 text-center font-semibold peer-checked:bg-[#6f4e37] peer-checked:text-white">{{ $method }}</span>
                </label>
            @endforeach
        </div>

        <label class="mb-1 mt-5 block text-sm font-semibold">Item Pesanan</label>
        @if ($menus->isEmpty())
            <p class="rounded-xl bg-amber-100 px-4 py-3 text-sm text-amber-900">
                Belum ada menu tersedia dengan resep. Periksa data menu dan resep terlebih dahulu.
            </p>
        @else
            <div id="items" class="space-y-3"></div>
            <button type="button" id="tambah" class="mt-3 rounded-xl bg-[#f3e6d6] px-4 py-3 font-semibold">+ Tambah Item</button>
        @endif
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
        <div class="mt-4 rounded-xl bg-[#f3e6d6] px-4 py-3 text-sm text-[#6b5645]">
            Menu dan harga dimuat dari database. Ketersediaan stok bahan akan diperiksa saat pesanan disimpan.
        </div>
        <button type="submit" @disabled($menus->isEmpty())
                class="mt-4 w-full rounded-xl bg-[#6f4e37] py-4 text-lg font-bold text-white hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
            Simpan Pesanan
        </button>
    </div>
</form>

@if (session('createdOrder'))
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/45">
        <div class="w-[440px] rounded-2xl bg-white p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#dcf2e2] text-3xl text-[#1f6b3a]">✓</div>
            <div class="mt-4 text-xl font-bold">Pesanan berhasil disimpan</div>
            <p class="mt-2 text-sm text-[#6b5645]">
                Pesanan atas nama <span class="font-semibold">{{ session('createdOrder.nama') }}</span> masuk ke antrean.
            </p>
            <div class="my-4 rounded-xl bg-[#f3e6d6] py-4 text-2xl font-extrabold tracking-[0.3em]">
                {{ session('createdOrder.kode') }}
            </div>
            <div class="mb-5 text-sm text-[#6b5645]">
                Total <b>Rp {{ number_format(session('createdOrder.total'), 0, ',', '.') }}</b>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('kasir.dashboard') }}"
                   class="rounded-xl border border-[#6f4e37] py-3 font-bold text-[#6f4e37]">Kembali ke Dashboard</a>
                <a href="{{ route('kasir.input') }}" class="rounded-xl bg-[#6f4e37] py-3 font-bold text-white">Buat Pesanan Baru</a>
            </div>
        </div>
    </div>
@endif

@if ($menus->isNotEmpty())
    <template id="item-template">
        <div class="flex gap-3">
            <select class="menu flex-1 rounded-xl border border-[#d9cdbf] bg-white px-4 py-3" required>
                @foreach ($menus as $menu)
                    <option value="{{ $menu->id }}" data-price="{{ $menu->harga }}">
                        {{ $menu->nama_menu }} — Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            <input type="number" min="1" max="100" value="1" class="qty w-24 rounded-xl border border-[#d9cdbf] px-3 py-3 text-center" required>
            <button type="button" class="hapus rounded-xl border border-[#d9cdbf] px-4 text-[#9b2c1f]" aria-label="Hapus item">✕</button>
        </div>
    </template>
@endif
@endsection

@if ($menus->isNotEmpty())
    @push('scripts')
    <script>
        const items = document.getElementById('items');
        const template = document.getElementById('item-template');
        const oldItems = @json(old('items', []));
        let nextIndex = 0;
        const rp = amount => 'Rp ' + Math.round(amount).toLocaleString('id-ID');

        function tambahBaris(selectedId = null, quantity = 1) {
            const fragment = template.content.cloneNode(true);
            const row = fragment.querySelector('div.flex');
            const select = row.querySelector('.menu');
            const input = row.querySelector('.qty');
            select.name = `items[${nextIndex}][id_menu]`;
            input.name = `items[${nextIndex}][jumlah]`;
            if (selectedId !== null) select.value = String(selectedId);
            input.value = quantity;
            nextIndex++;
            items.appendChild(fragment);
            hitung();
        }

        function hitung() {
            let subtotal = 0;
            items.querySelectorAll('div.flex').forEach(row => {
                const price = Number(row.querySelector('.menu').selectedOptions[0]?.dataset.price ?? 0);
                const quantity = Number(row.querySelector('.qty').value) || 0;
                subtotal += price * quantity;
            });
            const service = subtotal * 0.05;
            const pajak = (subtotal + service) * 0.10;
            document.getElementById('subtotal').textContent = rp(subtotal);
            document.getElementById('service').textContent = rp(service);
            document.getElementById('pajak').textContent = rp(pajak);
            document.getElementById('total').textContent = rp(subtotal + service + pajak);
        }

        document.getElementById('tambah').addEventListener('click', () => tambahBaris());
        items.addEventListener('input', hitung);
        items.addEventListener('change', hitung);
        items.addEventListener('click', event => {
            if (event.target.closest('.hapus')) {
                event.target.closest('div.flex').remove();
                hitung();
            }
        });

        if (Array.isArray(oldItems) && oldItems.length) {
            oldItems.forEach(item => tambahBaris(item.id_menu, item.jumlah));
        } else {
            tambahBaris();
        }
    </script>
    @endpush
@endif
