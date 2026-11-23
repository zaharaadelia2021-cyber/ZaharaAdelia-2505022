<?php

namespace App\Http\Controllers;

use App\Services\RestockService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request, RestockService $restockService): View
    {
        $pengaturan = $request->user()->pengaturan()->firstOrCreate([], ['hari_target_stok' => 3]);

        return view('pages.home', [
            ...$restockService->overview($request->user(), $pengaturan->hari_target_stok),
            'hariTargetStok' => $pengaturan->hari_target_stok,
        ]);
    }
}
