<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'user_id',
        'nama',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'integer',
            'harga_jual' => 'integer',
            'stok' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
        ];
    }

    protected function status(): Attribute
    {
        return Attribute::get(fn (): string => match (true) {
            (float) $this->stok === 0.0 => 'Habis',
            (float) $this->stok <= (float) $this->stok_minimum => 'Menipis',
            default => 'Aman',
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
