<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulanTahun extends Model
{
    protected $table = 'bulan_tahun';

    protected $guarded = ['id'];

    public $timestamps = false;
}
