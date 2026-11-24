<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTargetStokRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $pengaturan = $user->pengaturan()->firstOrCreate([], ['hari_target_stok' => 3]);

        return view('pages.profile', [
            'user' => $user,
            'hariTargetStok' => $pengaturan->hari_target_stok,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_warung' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($request->user()->id),
            ],
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile')->with('success', 'Data warung berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return Redirect::route('profile')->with('success', 'Kata sandi berhasil diubah.');
    }

    public function updateTarget(UpdateTargetStokRequest $request): RedirectResponse
    {
        $request->user()->pengaturan()->updateOrCreate([], $request->validated());

        return Redirect::route('profile')->with('success', 'Target stok berhasil disimpan.');
    }
}
