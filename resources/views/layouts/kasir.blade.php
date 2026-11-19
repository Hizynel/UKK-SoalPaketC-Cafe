<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#faf5ee] font-sans text-[#2a1f18]">
@php
    $nav = [
        ['kasir.dashboard', 'Dashboard'],
        ['kasir.input', 'Input Pesanan'],
        ['kasir.data', 'Data Pesanan'],
        ['kasir.menu', 'Menu'],
        ['kasir.akun', 'Akun Pelanggan'],
    ];
@endphp
<div class="flex min-h-screen">
    <aside class="flex w-64 shrink-0 flex-col bg-[#4a3426] px-4 py-8 text-[#f3e6d6]">
        <div class="px-2">
            <div class="text-xl font-bold text-white">Kedai Kopi Senja</div>
            <div class="text-xs text-[#cdb9a3]">Sistem Manajemen Kafe</div>
        </div>
        <nav class="mt-8 flex-1 space-y-1">
            @foreach ($nav as [$route, $label])
                <a href="{{ route($route) }}"
                   class="block rounded-xl px-4 py-3 {{ request()->routeIs($route) ? 'bg-[#6f4e37] font-semibold text-white' : 'hover:bg-[#5a4130]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full rounded-xl px-4 py-3 text-left hover:bg-[#5a4130]">Keluar</button>
        </form>
    </aside>

    <main class="flex-1">
        <header class="flex items-center justify-between border-b border-[#eadfd0] bg-white px-8 py-5">
            <h2 class="text-xl font-bold">@yield('title')</h2>
            <span class="rounded-full bg-[#f3e6d6] px-4 py-2 text-sm font-semibold">
                {{ auth()->user()->name ?? 'Rina' }} · Kasir
            </span>
        </header>
        <div class="p-8">@yield('content')</div>
    </main>
</div>
@stack('scripts')
</body>
</html>