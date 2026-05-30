<?php

namespace App\Repositories;

use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Models\NguyenLieu;
use App\Repositories\Contracts\BaoCaoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaoCaoRepository implements BaoCaoRepositoryInterface
{
    public function tongQuan(): array
    {
        return [
            'doanh_thu' => HoaDon::query()->whereIn('trang_thai', [HoaDon::TRANG_THAI_DA_THANH_TOAN, HoaDon::TRANG_THAI_DA_HOAN_THANH])->sum('tong_tien'),
            'so_hoa_don' => HoaDon::query()->count(),
            'hoa_don_cho_pha_che' => HoaDon::query()->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
            'nguyen_lieu_sap_het' => NguyenLieu::query()->whereColumn('ton_kho', '<=', 'so_luong_toi_thieu')->count(),
        ];
    }

    public function monBanChay(): Collection
    {
        return ChiTietHoaDon::query()
            ->select('ma_mon', DB::raw('SUM(so_luong) as tong_so_luong'))
            ->with('mon')
            ->groupBy('ma_mon')
            ->orderByDesc('tong_so_luong')
            ->limit(10)
            ->get();
    }

    public function nguyenLieuSapHet(): Collection
    {
        return NguyenLieu::query()->whereColumn('ton_kho', '<=', 'so_luong_toi_thieu')->orderBy('ton_kho')->limit(10)->get();
    }

    public function nguyenLieuTonNhieu(): Collection
    {
        return NguyenLieu::query()->orderByDesc('ton_kho')->limit(10)->get();
    }
}
