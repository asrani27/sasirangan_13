<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaAcuan extends Model
{
    protected $table = 'harga_acuan';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function pasar(): BelongsTo
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function bulanTahun(): BelongsTo
    {
        return $this->belongsTo(BulanTahun::class, 'bulan_tahun_id');
    }
}
