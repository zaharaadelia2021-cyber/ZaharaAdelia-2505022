<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTargetStokRequest;
use App\Services\RestockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BelanjaController extends Controller
{
    public function index(Request $request, RestockService $restockService): View
    {
        $pengaturan = $request->user()->pengaturan()->firstOrCreate([], ['hari_target_stok' => 3]);

        return view('pages.belanja', [
            'hariTargetStok' => $pengaturan->hari_target_stok,
            ...$restockService->overview($request->user(), $pengaturan->hari_target_stok),
        ]);
    }

    public function updateTarget(UpdateTargetStokRequest $request): RedirectResponse
    {
        $request->user()->pengaturan()->updateOrCreate([], $request->validated());

        return redirect()->route('belanja')->with('success', 'Target stok berhasil disimpan.');
    }
}
