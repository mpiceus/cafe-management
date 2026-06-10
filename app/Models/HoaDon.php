<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HoaDon extends Model
{
    public const TRANG_THAI_DANG_TAO = 'dang_tao';
    public const TRANG_THAI_DA_THANH_TOAN = 'da_thanh_toan';
    public const TRANG_THAI_DA_HOAN_THANH = 'da_hoan_thanh';

    protected $table = 'hoa_don';
    protected $primaryKey = 'ma_hoa_don';
    public $timestamps = false;

    protected $fillable = [
        'ma_nguoi_dung',
        'ma_thanh_toan',
        'thoi_gian_tao',
        'thoi_gian_thanh_toan',
        'tong_tien',
        'phuong_thuc_thanh_toan',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_tao' => 'datetime',
            'thoi_gian_thanh_toan' => 'datetime',
            'tong_tien' => 'decimal:2',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung', 'ma_nguoi_dung');
    }

    public function chiTiets(): HasMany
    {
        return $this->hasMany(ChiTietHoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function sepayTransactions(): HasMany
    {
        return $this->hasMany(SePayTransaction::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function sepayRefunds(): HasMany
    {
        return $this->hasMany(SePayRefund::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function trangThaiHienThi(): string
    {
        return match ($this->trang_thai) {
            self::TRANG_THAI_DANG_TAO => 'Chờ thanh toán',
            self::TRANG_THAI_DA_HOAN_THANH => 'Đã hoàn thành',
            self::TRANG_THAI_DA_THANH_TOAN => $this->trangThaiPhaCheHienThi(),
            default => $this->trang_thai,
        };
    }

    private function trangThaiPhaCheHienThi(): string
    {
        $this->loadMissing('chiTiets');

        if ($this->chiTiets->contains(fn ($item) => $item->trang_thai_pha_che === ChiTietHoaDon::PHA_CHE_DANG)) {
            return 'Đang pha chế';
        }

        if ($this->chiTiets->contains(fn ($item) => $item->trang_thai_pha_che === ChiTietHoaDon::PHA_CHE_CHO)) {
            return 'Chờ pha chế';
        }

        return 'Đã thanh toán';
    }
}
