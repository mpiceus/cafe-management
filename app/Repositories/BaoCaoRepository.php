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
    public function tongQuan(?string $tuNgay = null, ?string $denNgay = null): array
    {
        $hoaDonQuery = HoaDon::query()
            ->whereIn('trang_thai', [HoaDon::TRANG_THAI_DA_THANH_TOAN, HoaDon::TRANG_THAI_DA_HOAN_THANH]);

        if ($tuNgay) {
            $hoaDonQuery->whereDate('thoi_gian_tao', '>=', $tuNgay);
        }

        if ($denNgay) {
            $hoaDonQuery->whereDate('thoi_gian_tao', '<=', $denNgay);
        }

        return [
            'doanh_thu' => (clone $hoaDonQuery)->sum('tong_tien'),
            'so_hoa_don' => (clone $hoaDonQuery)->count(),
            'hoa_don_cho_pha_che' => HoaDon::query()->where('trang_thai', HoaDon::TRANG_THAI_DA_THANH_TOAN)->count(),
            'nguyen_lieu_sap_het' => NguyenLieu::query()->whereColumn('ton_kho', '<=', 'so_luong_toi_thieu')->count(),
        ];
    }

    public function monBanChay(?string $tuNgay = null, ?string $denNgay = null): Collection
    {
        $query = ChiTietHoaDon::query()
            ->select('ma_mon', DB::raw('SUM(so_luong) as tong_so_luong'))
            ->with('mon.congThucs.nguyenLieu')
            ->groupBy('ma_mon')
            ->orderByDesc('tong_so_luong');

        if ($tuNgay || $denNgay) {
            $query->whereHas('hoaDon', function ($hoaDonQuery) use ($tuNgay, $denNgay) {
                $hoaDonQuery->whereIn('trang_thai', [HoaDon::TRANG_THAI_DA_THANH_TOAN, HoaDon::TRANG_THAI_DA_HOAN_THANH]);

                if ($tuNgay) {
                    $hoaDonQuery->whereDate('thoi_gian_tao', '>=', $tuNgay);
                }

                if ($denNgay) {
                    $hoaDonQuery->whereDate('thoi_gian_tao', '<=', $denNgay);
                }
            });
        }

        return $query->limit(10)->get();
    }

    public function nguyenLieuSapHet(): Collection
    {
        return NguyenLieu::query()->whereColumn('ton_kho', '<=', 'so_luong_toi_thieu')->orderBy('ton_kho')->limit(10)->get();
    }

    public function nguyenLieuTonNhieu(): Collection
    {
        return NguyenLieu::query()->orderByDesc('ton_kho')->limit(10)->get();
    }

    public function doanhThuTheoNgay(?string $tuNgay = null, ?string $denNgay = null): Collection
    {
        $query = HoaDon::query()
            ->select(DB::raw('DATE(thoi_gian_tao) as ngay'), DB::raw('SUM(tong_tien) as doanh_thu'))
            ->whereIn('trang_thai', [HoaDon::TRANG_THAI_DA_THANH_TOAN, HoaDon::TRANG_THAI_DA_HOAN_THANH]);

        if ($tuNgay) {
            $query->whereDate('thoi_gian_tao', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->whereDate('thoi_gian_tao', '<=', $denNgay);
        }

        return $query
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();
    }

    public function doanhThuTheoThang(?string $tuNgay = null, ?string $denNgay = null): Collection
    {
        $query = HoaDon::query()
            ->select(DB::raw("DATE_FORMAT(thoi_gian_tao, '%Y-%m') as thang"), DB::raw('SUM(tong_tien) as doanh_thu'))
            ->whereIn('trang_thai', [HoaDon::TRANG_THAI_DA_THANH_TOAN, HoaDon::TRANG_THAI_DA_HOAN_THANH]);

        if ($tuNgay) {
            $query->whereDate('thoi_gian_tao', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->whereDate('thoi_gian_tao', '<=', $denNgay);
        }

        return $query
            ->groupBy('thang')
            ->orderBy('thang')
            ->get();
    }
}
