<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorAduan extends Model
{
    protected $table = 'nomor_aduan';

    protected $guarded = ['id'];

    public $timestamps = false;
}
