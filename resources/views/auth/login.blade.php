<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk – Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-[#2a1f18]">
    <div class="flex min-h-screen">
        <div class="hidden w-[58%] flex-col justify-end bg-[#4a3426] p-20 text-white lg:flex">
            <h1 class="text-5xl font-bold">Kedai Kopi Senja</h1>
            <p class="mt-4 max-w-md text-xl text-[#e6d5c3]">
                Kelola pesanan, stok bahan, dan laporan kafe dalam satu tempat.
            </p>
        </div>

        <div class="flex flex-1 items-center justify-center bg-[#faf5ee] px-8">
            <div class="w-full max-w-sm">
                <h1 class="text-3xl font-bold">Login Kasir</h1>

                <form action="{{ route('login') }}" method="post" class="mt-8 space-y-4">
                    @csrf
                    <div>
                        <label for="username" class="mb-1 block text-sm font-semibold">Username / Nomor HP</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                               class="w-full rounded-xl border border-[#d9cdbf] bg-white px-4 py-3 outline-none focus:border-[#6f4e37]">
                    </div>
                    <div>
                        <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full rounded-xl border border-[#d9cdbf] bg-white px-4 py-3 outline-none focus:border-[#6f4e37]">
                    </div>

                    @error('login')
                        <p class="text-sm text-[#9b2c1f]">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="w-full rounded-xl bg-[#6f4e37] py-4 font-semibold text-white hover:opacity-90">
                        Masuk
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-[#7a6658]"></p>
            </div>
        </div>
    </div>
</body>
</html>