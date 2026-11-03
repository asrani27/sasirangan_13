<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    protected $table = 'traffic';

    protected $guarded = ['id'];

    public $timestamps = false;
}
