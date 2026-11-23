<x-guest-layout title="Masuk">
    <section class="rounded-[1.75rem] border border-slate-100 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.22)] sm:p-8">
        <div class="mb-7">
            <p class="mb-2 text-sm font-semibold text-brand-600">Senang bertemu lagi</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Breeze</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Lanjutkan mengatur warungmu dengan lebih mudah.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi</label>
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-50">
                @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <label for="remember" class="flex items-center gap-2.5 py-1 text-sm text-slate-600">
                <input id="remember" name="remember" type="checkbox" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Ingat saya
            </label>
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
                Masuk
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-800">Daftar sekarang</a></p>
    </section>
</x-guest-layout>
