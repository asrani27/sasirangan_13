<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'kategori_id');
    }
}
