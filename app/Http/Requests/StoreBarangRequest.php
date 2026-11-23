<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('barang', 'nama')->where('user_id', $this->user()->id),
            ],
            'satuan' => ['required', 'string', 'max:255'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
            'stok_minimum' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999.99'],
        ];
    }
}
