<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(['terjual', 'kulakan'])],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('barang', 'id')->where('user_id', $this->user()->id),
            ],
            'items.*.jumlah' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:9999999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Pilih jenis catatan terlebih dahulu.',
            'jenis.in' => 'Jenis catatan harus Terjual atau Kulakan.',
            'tanggal.required' => 'Tanggal catatan wajib diisi.',
            'tanggal.date_format' => 'Tanggal catatan tidak valid.',
            'items.required' => 'Tambahkan minimal satu barang.',
            'items.min' => 'Tambahkan minimal satu barang.',
            'items.*.barang_id.required' => 'Pilih barang untuk baris ini.',
            'items.*.barang_id.exists' => 'Barang yang dipilih tidak tersedia di warungmu.',
            'items.*.barang_id.distinct' => 'Barang yang sama tidak boleh ditambahkan lebih dari sekali.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.jumlah.numeric' => 'Jumlah barang harus berupa angka.',
            'items.*.jumlah.gt' => 'Jumlah barang harus lebih dari nol.',
            'items.*.jumlah.decimal' => 'Jumlah barang maksimal memiliki dua angka di belakang koma.',
            'items.*.jumlah.max' => 'Jumlah barang melebihi batas yang diperbolehkan.',
        ];
    }
}
