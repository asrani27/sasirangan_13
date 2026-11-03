<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Cronjob extends Model
{
    use Prunable;

    protected $table = 'cronjob';

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Get the prunable model query (hapus log yang lebih dari 30 hari).
     */
    public function prunable(): Builder
    {
        return static::where('cronjob', '<=', now()->subDays(30)->format('Y-m-d H:i:s'));
    }
}
