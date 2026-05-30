<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiaMon extends Model
{
    protected $table = 'gia_mon';

    protected $primaryKey = 'ma_gia_mon';

    public $timestamps = false;

    protected $fillable = [
        'ma_mon',
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
