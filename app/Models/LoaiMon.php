<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiMon extends Model
{
    public const MA_TOPPING = 5;

    protected $table = 'loai_mon';

    protected $primaryKey = 'ma_loai_mon';

    public $timestamps = false;

    protected $fillable = [
        'ten_loai_mon',
        'mo_ta',
    ];

    public function mons(): HasMany
    {
        return $this->hasMany(Mon::class, 'ma_loai_mon', 'ma_loai_mon');
    }
}
