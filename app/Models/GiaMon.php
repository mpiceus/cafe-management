<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiaMon extends Model
{
    public const SIZE_S = 'S';
    public const SIZE_M = 'M';
    public const SIZE_L = 'L';

    protected $table = 'gia_mon';

    protected $primaryKey = 'ma_mon';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ma_mon',
        'size',
        'gia',
        'ngay_ap_dung',
    ];

    protected function casts(): array
    {
        return [
            'gia' => 'decimal:2',
            'ngay_ap_dung' => 'datetime',
        ];
    }

    public function mon(): BelongsTo
    {
        return $this->belongsTo(Mon::class, 'ma_mon', 'ma_mon');
    }
}
