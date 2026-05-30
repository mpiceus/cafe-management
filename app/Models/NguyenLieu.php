<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NguyenLieu extends Model
{
    protected $table = 'nguyen_lieu';
    protected $primaryKey = 'ma_nguyen_lieu';
    public $timestamps = false;

    protected $fillable = [
        'ten_nguyen_lieu',
        'don_vi_tinh',
        'ton_kho',
        'so_luong_toi_thieu',
        'ma_nha_cung_cap',
        'ti_le_su_dung',
        'duoc_tuy_chinh',
    ];

    protected function casts(): array
    {
        return [
            'ton_kho' => 'decimal:2',
            'so_luong_toi_thieu' => 'decimal:2',
            'ti_le_su_dung' => 'decimal:2',
            'duoc_tuy_chinh' => 'boolean',
        ];
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap', 'ma_nha_cung_cap');
    }

    public function congThucs(): HasMany
    {
        return $this->hasMany(CongThuc::class, 'ma_nguyen_lieu', 'ma_nguyen_lieu');
    }
}
