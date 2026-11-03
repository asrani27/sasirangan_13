<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelompok extends Model
{
    protected $table = 'kelompok';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function bahan(): HasMany
    {
        return $this->hasMany(Bahan::class, 'kelompok_id');
    }
}
