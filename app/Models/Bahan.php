<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    protected $table = 'bahan';

    protected $guarded = ['id'];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function pasar(): BelongsToMany
    {
        return $this->belongsToMany(Pasar::class, 'bahan_pasar', 'bahan_id', 'pasar_id');
    }

    public function harga(): HasMany
    {
        return $this->hasMany(Harga::class, 'bahan_id');
    }

    public function stok_kota(): HasMany
    {
        return $this->hasMany(Stok_kota::class, 'bahan_id');
    }
}
