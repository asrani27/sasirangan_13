<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasar extends Model
{
    protected $table = 'pasar';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function bahan(): BelongsToMany
    {
        return $this->belongsToMany(Bahan::class, 'bahan_pasar', 'pasar_id', 'bahan_id');
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pasar_users', 'pasar_id', 'user_id');
    }

    public function hargaToday(): HasMany
    {
        return $this->hasMany(Harga::class, 'pasar_id')->where('tanggal', today()->format('Y-m-d'));
    }

    public function harga(): HasMany
    {
        return $this->hasMany(Harga::class, 'pasar_id');
    }
}
