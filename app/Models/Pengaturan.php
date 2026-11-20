<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'user_id',
        'hari_target_stok',
    ];

    protected function casts(): array
    {
        return [
            'hari_target_stok' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
