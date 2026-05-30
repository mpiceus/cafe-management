<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDonNhap extends Model
{
    protected $table = 'chi_tiet_don_nhap';
    protected $primaryKey = 'ma_chi_tiet_nhap';
    public $timestamps = false;

    protected $fillable = [
        'ma_don_nhap',
        'ma_nguyen_lieu',
        'so_luong',
        'don_vi_mua',
        'so_luong_nhap_kho',
        'don_gia',
    ];

    protected function casts(): array
    {
        return [
            'so_luong' => 'decimal:2',
            'so_luong_nhap_kho' => 'decimal:2',
            'don_gia' => 'decimal:2',
        ];
    }

    public function nguyenLieu(): BelongsTo
    {
        return $this->belongsTo(NguyenLieu::class, 'ma_nguyen_lieu', 'ma_nguyen_lieu');
    }
}
