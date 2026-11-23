<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Barang::class);

        $userBarangs = $request->user()->barangs();
        $counts = [
            'semua' => (clone $userBarangs)->count(),
            'menipis' => (clone $userBarangs)->where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum')->count(),
            'habis' => (clone $userBarangs)->where('stok', 0)->count(),
        ];

        $query = $request->user()->barangs()->orderBy('nama');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        match ($request->query('status')) {
            'menipis' => $query->where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum'),
            'habis' => $query->where('stok', 0),
            default => null,
        };

        return view('pages.barang', [
            'barangs' => $query->get(),
            'counts' => $counts,
            'search' => $search,
            'status' => in_array($request->query('status'), ['menipis', 'habis'], true)
                ? $request->query('status')
                : 'semua',
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Barang::class);

        return view('pages.barang-form', ['barang' => new Barang(), 'mode' => 'create']);
    }

    public function store(StoreBarangRequest $request): RedirectResponse
    {
        Gate::authorize('create', Barang::class);

        $request->user()->barangs()->create($request->validated());

        return redirect()->route('barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang): View
    {
        Gate::authorize('update', $barang);

        return view('pages.barang-form', ['barang' => $barang, 'mode' => 'edit']);
    }

    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $barang->update($request->validated());

        return redirect()->route('barang')->with('success', 'Perubahan barang berhasil disimpan.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        Gate::authorize('delete', $barang);
        $barang->delete();

        return redirect()->route('barang')->with('success', 'Barang berhasil dihapus.');
    }
}
