<x-mobile-layout title="Profil" active-tab="profile">
    <div>
        <p class="text-sm font-medium text-brand-700">Pengaturan akun</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Profil</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola identitas warung dan preferensi stok.</p>
    </div>

    @if (session('success'))
        <p role="status" class="mt-5 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">{{ session('success') }}</p>
    @endif

    <section aria-label="Identitas warung dan pemilik" class="mt-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18M5 20V9l7-5 7 5v11M9 20v-6h6v6" /></svg>
        </div>
        <p class="mt-4 text-xs font-medium uppercase tracking-wide text-slate-400">Nama warung</p>
        <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $user->nama_warung }}</h2>
        <div class="mt-4 border-t border-slate-100 pt-4">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Pemilik</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->name }}</p>
            <p class="mt-0.5 text-sm text-slate-500">{{ $user->email }}</p>
        </div>
    </section>

    <section class="mt-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <form method="POST" action="{{ route('profile.target.update') }}">
            @csrf
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Target stok</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Target hari yang sama dengan layar Belanja</p>
                </div>
                <div class="flex items-center rounded-xl border border-slate-200">
                    <button type="button" data-target-step="-1" aria-label="Kurangi hari target" class="flex size-10 items-center justify-center rounded-l-xl text-slate-600 transition hover:bg-slate-50">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M5 12h14" /></svg>
                    </button>
                    <input name="hari_target_stok" data-target-input type="number" value="{{ old('hari_target_stok', $hariTargetStok) }}" min="1" max="65535" step="1" required aria-label="Target stok dalam hari" class="w-12 border-0 bg-transparent p-0 text-center text-sm font-bold tabular-nums text-slate-900 outline-none focus:ring-0">
                    <span class="pr-1 text-xs text-slate-500">hari</span>
                    <button type="button" data-target-step="1" aria-label="Tambah hari target" class="flex size-10 items-center justify-center rounded-r-xl text-slate-600 transition hover:bg-slate-50">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14m-7-7h14" /></svg>
                    </button>
                </div>
            </div>
            @error('hari_target_stok') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            <button type="submit" class="mt-3 w-full rounded-xl bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">Simpan target</button>
        </form>
        <p class="mt-3 rounded-xl bg-brand-50 px-3 py-2.5 text-xs leading-relaxed text-brand-800">Rekomendasi dihitung dari rata-rata jumlah barang terjual per hari selama 7 hari terakhir.</p>
    </section>

    <section aria-label="Menu profil" class="mt-5 space-y-3">
        <details class="group rounded-2xl border border-slate-100 bg-white shadow-sm" @if ($errors->hasAny(['nama_warung', 'name', 'email'])) open @endif>
            <summary class="flex cursor-pointer list-none items-center justify-between p-4">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Ubah data warung</span>
                    <span class="mt-0.5 block text-xs text-slate-500">Nama warung dan identitas pemilik</span>
                </span>
                <svg class="size-4 text-slate-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
            </summary>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-3 border-t border-slate-100 p-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="nama_warung" class="text-xs font-medium text-slate-600">Nama warung</label>
                    <input id="nama_warung" name="nama_warung" value="{{ old('nama_warung', $user->nama_warung) }}" required maxlength="255" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('nama_warung') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="name" class="text-xs font-medium text-slate-600">Nama pemilik</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="text-xs font-medium text-slate-600">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Simpan data</button>
            </form>
        </details>

        <details class="group rounded-2xl border border-slate-100 bg-white shadow-sm" @if ($errors->hasAny(['current_password', 'password'])) open @endif>
            <summary class="flex cursor-pointer list-none items-center justify-between p-4">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Ganti kata sandi</span>
                    <span class="mt-0.5 block text-xs text-slate-500">Perbarui kata sandi akun</span>
                </span>
                <svg class="size-4 text-slate-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
            </summary>
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-3 border-t border-slate-100 p-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="current_password" class="text-xs font-medium text-slate-600">Kata sandi saat ini</label>
                    <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="text-xs font-medium text-slate-600">Kata sandi baru</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="text-xs font-medium text-slate-600">Ulangi kata sandi baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <button type="submit" class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Simpan kata sandi</button>
            </form>
        </details>
    </section>

    <form method="POST" action="{{ route('logout') }}" class="mt-5">
        @csrf
        <button type="submit" class="w-full rounded-xl border border-red-200 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50">Keluar</button>
    </form>
</x-mobile-layout>
