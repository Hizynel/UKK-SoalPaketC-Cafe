@extends('layouts.kasir')
@section('title', 'Data Pesanan / Riwayat')
@section('content')
@if (session('error') || session('success'))
    <div class="mb-5 rounded-xl {{ session('error') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} px-4 py-3">
        {{ session('error') ?? session('success') }}
    </div>
@endif
<div class="mb-5 flex gap-2" id="tabs">
    @foreach (['semua' => 'Semua', 'baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $filter => $label)
        <button data-filter="{{ $filter }}"
                class="tab rounded-full border border-[#d9cdbf] px-4 py-2 font-semibold {{ $loop->first ? 'bg-[#6f4e37] text-white' : 'bg-white' }}">
            {{ $label }} ({{ $filter === 'semua' ? $pesanan->count() : $pesanan->where('status_pesanan', $filter)->count() }})
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
        @forelse ($pesanan as $item)
            @php
                $badge = [
                    'baru' => 'bg-[#fdf0d0] text-[#8a5a00]',
                    'diproses' => 'bg-[#dce8fb] text-[#1d4e89]',
                    'selesai' => 'bg-[#dcf2e2] text-[#1f6b3a]',
                    'dibatalkan' => 'bg-gray-100 text-gray-600',
                ];
                $status = $item->status_pesanan;
            @endphp
            <tr class="baris border-t border-[#eadfd0]" data-status="{{ $status }}">
                <td class="px-6 py-4 font-bold">{{ $item->kode_unik }}</td>
                <td class="px-6 py-4">{{ $item->nama_pelanggan }}</td>
                <td class="px-6 py-4">Meja {{ $item->nomor_meja }}</td>
                <td class="px-6 py-4">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($status) }}</span></td>
                <td class="px-6 py-4 text-right">
                    @if ($status === 'baru')
                        <form method="POST" action="{{ route('kasir.proses', $item) }}" class="inline">
                            @csrf
                            <button class="rounded-lg bg-[#1d4e89] px-4 py-2 font-semibold text-white">Proses</button>
                        </form>
                        <form method="POST" action="{{ route('kasir.batal', $item) }}" class="ml-1 inline">
                            @csrf
                            <button class="rounded-lg border border-[#9b2c1f] px-4 py-2 font-semibold text-[#9b2c1f]">Batalkan</button>
                        </form>
                    @elseif ($status === 'diproses')
                        <form method="POST" action="{{ route('kasir.selesai', $item) }}" class="inline">
                            @csrf
                            <button class="rounded-lg bg-[#1f6b3a] px-4 py-2 font-semibold text-white">Selesaikan</button>
                        </form>
                    @elseif ($status === 'selesai')
                        <a href="{{ route('kasir.struk', $item) }}" class="rounded-lg bg-[#f3e6d6] px-4 py-2 font-semibold">Lihat Struk</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-[#6b5645]">Belum ada data pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<p class="mt-4 text-sm text-[#6b5645]">
    Status berubah berjenjang: Baru → Diproses → Selesai. Pembatalan hanya untuk pesanan Baru dan stok bahan kembali otomatis.
</p>

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
            r.style.display = (f === 'semua' || r.dataset.status === f) ? '' : 'none');
    });
</script>
@endpush