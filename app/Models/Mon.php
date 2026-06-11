<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mon extends Model
{
    public const CHE_DO_CA_HAI = 'ca_hai';
    public const CHE_DO_CHI_NONG = 'chi_nong';
    public const CHE_DO_CHI_LANH = 'chi_lanh';
    public const CHE_DO_KHONG_AP_DUNG = 'khong_ap_dung';

    public const TRANG_THAI_DANG_BAN = 'dang_ban';
    public const TRANG_THAI_DUNG_BAN = 'dung_ban';

    protected $table = 'mon';

    protected $primaryKey = 'ma_mon';

    public $timestamps = false;

    protected $fillable = [
        'ma_loai_mon',
        'ten_mon',
        'mo_ta',
        'hinh_anh',
        'che_do_phuc_vu',
        'cho_them_topping',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'cho_them_topping' => 'boolean',
        ];
    }

    public function loaiMon(): BelongsTo
    {
        return $this->belongsTo(LoaiMon::class, 'ma_loai_mon', 'ma_loai_mon');
    }

    public function giaMons(): HasMany
    {
        return $this->hasMany(GiaMon::class, 'ma_mon', 'ma_mon');
    }

    public function congThucs(): HasMany
    {
        return $this->hasMany(CongThuc::class, 'ma_mon', 'ma_mon');
    }

    public function chiTietHoaDons(): HasMany
    {
        return $this->hasMany(ChiTietHoaDon::class, 'ma_mon', 'ma_mon');
    }

    public function chiTietToppings(): HasMany
    {
        return $this->hasMany(ChiTietTopping::class, 'ma_mon', 'ma_mon');
    }

    public function giaMoiNhat(): HasOne
    {
        return $this->hasOne(GiaMon::class, 'ma_mon', 'ma_mon')
            ->where('size', GiaMon::SIZE_S);
    }

    public function giaSizeS(): HasOne
    {
        return $this->hasOne(GiaMon::class, 'ma_mon', 'ma_mon')
            ->where('size', GiaMon::SIZE_S);
    }

    public function giaSizeM(): HasOne
    {
        return $this->hasOne(GiaMon::class, 'ma_mon', 'ma_mon')
            ->where('size', GiaMon::SIZE_M);
    }

    public function giaSizeL(): HasOne
    {
        return $this->hasOne(GiaMon::class, 'ma_mon', 'ma_mon')
            ->where('size', GiaMon::SIZE_L);
    }

    public function giaTheoSize(string $size): ?GiaMon
    {
        $size = strtoupper($size);

        if ($this->relationLoaded('giaMons')) {
            return $this->giaMons->firstWhere('size', $size);
        }

        return $this->giaMons()->where('size', $size)->first();
    }

    public function dangBan(): bool
    {
        return $this->trang_thai === self::TRANG_THAI_DANG_BAN;
    }
}
