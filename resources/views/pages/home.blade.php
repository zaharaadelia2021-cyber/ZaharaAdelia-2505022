<x-mobile-layout title="Beranda" active-tab="home">
    <p class="text-sm font-medium text-brand-700">Halo, {{ auth()->user()->name }} 👋</p>
    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Beranda</h1>
    <p class="mt-2 text-sm text-slate-500">{{ auth()->user()->nama_warung }}</p>
</x-mobile-layout>
