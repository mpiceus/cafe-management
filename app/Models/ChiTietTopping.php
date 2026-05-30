<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietTopping extends Model
{
    protected $table = 'chi_tiet_topping';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['ma_chi_tiet', 'ma_mon', 'so_luong'];

    public function mon(): BelongsTo
    {
        return $this->belongsTo(Mon::class, 'ma_mon', 'ma_mon');
    }
}
