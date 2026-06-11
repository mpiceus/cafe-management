<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChiTietHoaDon extends Model
{
    public const PHA_CHE_CHO = 'cho_pha_che';
    public const PHA_CHE_DANG = 'dang_pha_che';
    public const PHA_CHE_XONG = 'da_hoan_thanh';

    protected $table = 'chi_tiet_hoa_don';
    protected $primaryKey = 'ma_chi_tiet';
    public $timestamps = false;

    protected $fillable = ['ma_hoa_don', 'ma_mon', 'size', 'so_luong', 'che_do', 'ghi_chu', 'trang_thai_pha_che'];

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'ma_hoa_don', 'ma_hoa_don');
    }

    public function mon(): BelongsTo
    {
        return $this->belongsTo(Mon::class, 'ma_mon', 'ma_mon');
    }

    public function tuyChinhs(): HasMany
    {
        return $this->hasMany(ChiTietTuyChinh::class, 'ma_ct', 'ma_chi_tiet');
    }

    public function toppings(): HasMany
    {
        return $this->hasMany(ChiTietTopping::class, 'ma_chi_tiet', 'ma_chi_tiet');
    }
}
