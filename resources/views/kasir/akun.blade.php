@extends('layouts.kasir')
@section('title', 'Manajemen Akun Pelanggan')
@section('content')
<form method="GET" action="{{ route('kasir.akun') }}" class="mb-5 flex gap-2">
    <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama atau nomor HP"
           class="w-80 rounded-xl border border-[#d9cdbf] bg-white px-4 py-3 outline-none focus:border-[#6f4e37]">
    <button class="rounded-xl bg-[#6f4e37] px-5 py-3 font-semibold text-white">Cari</button>
    @if ($q)
        <a href="{{ route('kasir.akun') }}" class="rounded-xl border border-[#d9cdbf] px-5 py-3 font-semibold">Reset</a>
    @endif
</form>

<div class="overflow-hidden rounded-2xl border border-[#eadfd0] bg-white">
    <table class="w-full text-left">
        <thead class="bg-[#f3e6d6] text-sm text-[#6b5645]">
            <tr><th class="px-6 py-3">Nama</th><th class="px-6 py-3">Nomor HP</th><th class="px-6 py-3">Terdaftar</th><th></th></tr>
        </thead>
        <tbody>
        @forelse ($pelanggan as $user)
            <tr class="border-t border-[#eadfd0]">
                <td class="px-6 py-4 font-bold">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->nomer_telepon }}</td>
                <td class="px-6 py-4">{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
                <td class="px-6 py-4 text-right">
                    <form method="POST" action="{{ route('kasir.akun.reset', $user) }}">
                        @csrf
                        @if ($q)
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        <button class="rounded-lg bg-[#7a5416] px-4 py-2 font-semibold text-white">Reset Password</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-[#6b5645]">Tidak ada akun pelanggan yang cocok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if (session('reset'))
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/45">
        <div class="w-[440px] rounded-2xl bg-white p-8">
            <div class="text-lg font-bold">Password sementara dibuat</div>
            <p class="mt-2 text-sm text-[#6b5645]">
                Berikan ke <span class="font-semibold">{{ session('reset.nama') }}</span>. Pelanggan wajib menggantinya saat login berikutnya.
            </p>
            <div class="my-4 rounded-xl bg-[#f3e6d6] py-4 text-center text-3xl font-extrabold tracking-[0.3em]">
                {{ session('reset.password') }}
            </div>
            <a href="{{ route('kasir.akun', request()->only('q')) }}"
               class="block w-full rounded-xl bg-[#6f4e37] py-3 text-center font-bold text-white">Selesai</a>
        </div>
    </div>
@endif
@endsection
