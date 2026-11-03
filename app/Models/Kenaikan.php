<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kenaikan extends Model
{
    protected $table = 'kenaikan';

    protected $guarded = ['id'];

    public function pasar(): BelongsTo
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
