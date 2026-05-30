<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const CHUC_VU_CHU_CUA_HANG = 'chu_cua_hang';
    public const CHUC_VU_NHAN_VIEN_ORDER = 'nhan_vien_order';
    public const CHUC_VU_NHAN_VIEN_PHA_CHE = 'nhan_vien_pha_che';

    public const TRANG_THAI_HOAT_DONG = 'hoat_dong';
    public const TRANG_THAI_NGUNG_HOAT_DONG = 'ngung_hoat_dong';

    protected $table = 'nguoi_dung';

    protected $primaryKey = 'ma_nguoi_dung';

    public $timestamps = false;

    protected $fillable = [
        'ho_ten',
        'ten_dang_nhap',
        'mat_khau',
        'chuc_vu',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected function casts(): array
    {
        return [
            'mat_khau' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->mat_khau;
    }

    public function isChuCuaHang(): bool
    {
        return $this->chuc_vu === self::CHUC_VU_CHU_CUA_HANG;
    }

    public function dangHoatDong(): bool
    {
        return $this->trang_thai === self::TRANG_THAI_HOAT_DONG;
    }
}
