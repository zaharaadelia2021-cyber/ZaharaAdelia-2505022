<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTargetStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'hari_target_stok' => ['required', 'integer', 'min:1', 'max:65535'],
        ];
    }

    public function messages(): array
    {
        return [
            'hari_target_stok.required' => 'Target stok wajib diisi.',
            'hari_target_stok.integer' => 'Target stok harus berupa jumlah hari bulat.',
            'hari_target_stok.min' => 'Target stok minimal 1 hari.',
            'hari_target_stok.max' => 'Target stok melebihi batas yang diperbolehkan.',
        ];
    }
}
