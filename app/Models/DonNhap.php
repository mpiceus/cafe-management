<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonNhap extends Model
{
    protected $table = 'don_nhap';
    protected $primaryKey = 'ma_don_nhap';
    public $timestamps = false;

    protected $fillable = ['ma_nha_cung_cap', 'ma_nguoi_dung', 'tong_tien', 'ngay_nhap'];

    protected function casts(): array
    {
        return [
            'tong_tien' => 'decimal:2',
            'ngay_nhap' => 'datetime',
        ];
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap', 'ma_nha_cung_cap');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung', 'ma_nguoi_dung');
    }

    public function chiTiets(): HasMany
    {
        return $this->hasMany(ChiTietDonNhap::class, 'ma_don_nhap', 'ma_don_nhap');
    }
}
