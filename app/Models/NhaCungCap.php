<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    protected $table = 'nha_cung_cap';
    protected $primaryKey = 'ma_nha_cung_cap';
    public $timestamps = false;

    protected $fillable = ['ten_nha_cung_cap', 'so_dien_thoai', 'email', 'dia_chi'];

    public function nguyenLieus(): HasMany
    {
        return $this->hasMany(NguyenLieu::class, 'ma_nha_cung_cap', 'ma_nha_cung_cap');
    }

    public function donNhaps(): HasMany
    {
        return $this->hasMany(DonNhap::class, 'ma_nha_cung_cap', 'ma_nha_cung_cap');
    }
}
