<?php

namespace App\Services;

use App\Models\NguyenLieu;
use App\Repositories\Contracts\BaoCaoRepositoryInterface;
use Illuminate\Support\Collection;

class BaoCaoService
{
    public function __construct(private readonly BaoCaoRepositoryInterface $repository) {}

    public function duLieu(array $filters = []): array
    {
        $tuNgay = $filters['tu_ngay'] ?? null;
        $denNgay = $filters['den_ngay'] ?? null;
        $goiYKhoFilters = $this->normalizeGoiYKhoFilters($filters);
        $goiYKhoDenNgay = now()->toDateString();
        $goiYKhoTuNgay = now()->subDays($goiYKhoFilters['so_ngay_quan_sat'] - 1)->toDateString();

        return [
            'tongQuan' => $this->repository->tongQuan($tuNgay, $denNgay),
            'monBanChay' => $this->repository->monBanChay($tuNgay, $denNgay),
            'nguyenLieuSapHet' => $this->repository->nguyenLieuSapHet(),
            'nguyenLieuTonNhieu' => $this->repository->nguyenLieuTonNhieu(),
            'doanhThuTheoNgay' => $this->repository->doanhThuTheoNgay($tuNgay, $denNgay),
            'doanhThuTheoThang' => $this->repository->doanhThuTheoThang($tuNgay, $denNgay),
            'filters' => ['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay],
            'goiYNhapKho' => $this->goiYNhapKho(
                $this->repository->nguyenLieus(),
                $this->repository->mucTieuThuMon($goiYKhoTuNgay, $goiYKhoDenNgay),
                $goiYKhoFilters
            ),
            'goiYKhoRange' => ['tu_ngay' => $goiYKhoTuNgay, 'den_ngay' => $goiYKhoDenNgay],
            'goiYKhoFilters' => $goiYKhoFilters,
            'nhaCungCaps' => $this->repository->nhaCungCaps(),
        ];
    }

    private function normalizeGoiYKhoFilters(array $filters): array
    {
        $soNgayQuanSat = (int) ($filters['so_ngay_quan_sat'] ?? 7);
        $soNgayDuTru = (int) ($filters['so_ngay_du_tru'] ?? 7);

        return [
            'so_ngay_quan_sat' => in_array($soNgayQuanSat, [7, 14, 30], true) ? $soNgayQuanSat : 7,
            'so_ngay_du_tru' => in_array($soNgayDuTru, [3, 7, 14], true) ? $soNgayDuTru : 7,
            'ma_nha_cung_cap' => filled($filters['ma_nha_cung_cap'] ?? null)
                ? (int) $filters['ma_nha_cung_cap']
                : null,
            'chi_can_nhap' => (string) ($filters['chi_can_nhap'] ?? '1') !== '0',
        ];
    }

    private function goiYNhapKho(Collection $nguyenLieus, Collection $mucTieuThuMon, array $filters): Collection
    {
        $usage = collect();

        foreach ($mucTieuThuMon as $item) {
            foreach ($item->mon?->congThucs ?? [] as $congThuc) {
                if (! $congThuc->nguyenLieu) {
                    continue;
                }

                $key = $congThuc->nguyenLieu->ma_nguyen_lieu;
                $usage[$key] = ($usage[$key] ?? 0)
                    + ((float) $congThuc->so_luong * (float) $item->tong_so_luong);
            }
        }

        return $nguyenLieus
            ->map(function ($nguyenLieu) use ($usage, $filters) {
                $tonKho = (float) $nguyenLieu->ton_kho;
                $tonToiThieu = (float) $nguyenLieu->so_luong_toi_thieu;
                $tieuThuTrungBinhNgay = (float) ($usage[$nguyenLieu->ma_nguyen_lieu] ?? 0)
                    / $filters['so_ngay_quan_sat'];
                $mucTieuTonKho = $tonToiThieu + ($tieuThuTrungBinhNgay * $filters['so_ngay_du_tru']);
                $deXuat = max(0, $mucTieuTonKho - $tonKho);

                if ($tonKho <= $tonToiThieu && $deXuat <= 0) {
                    $deXuat = $tonToiThieu;
                }

                $soNgayConLai = $tieuThuTrungBinhNgay > 0 ? $tonKho / $tieuThuTrungBinhNgay : null;
                [$mucUuTien, $nhanUuTien] = $this->mucUuTien($tonKho, $tonToiThieu, $soNgayConLai, $deXuat);
                [$soLuongMua, $donViMua] = $this->soLuongMua($nguyenLieu, $deXuat);

                return [
                    'ma_nguyen_lieu' => (int) $nguyenLieu->ma_nguyen_lieu,
                    'ten' => $nguyenLieu->ten_nguyen_lieu,
                    'ma_nha_cung_cap' => $nguyenLieu->ma_nha_cung_cap ? (int) $nguyenLieu->ma_nha_cung_cap : null,
                    'nha_cung_cap' => $nguyenLieu->nhaCungCap?->ten_nha_cung_cap ?? 'Chưa có nhà cung cấp',
                    'ton_kho' => $tonKho,
                    'ton_toi_thieu' => $tonToiThieu,
                    'tieu_thu_trung_binh_ngay' => $tieuThuTrungBinhNgay,
                    'so_ngay_con_lai' => $soNgayConLai,
                    'de_xuat' => $deXuat,
                    'don_vi' => $nguyenLieu->don_vi_tinh,
                    'so_luong_mua' => $soLuongMua,
                    'don_vi_mua' => $donViMua,
                    'muc_uu_tien' => $mucUuTien,
                    'nhan_uu_tien' => $nhanUuTien,
                ];
            })
            ->filter(fn ($row) => ! $filters['ma_nha_cung_cap']
                || $row['ma_nha_cung_cap'] === $filters['ma_nha_cung_cap'])
            ->filter(fn ($row) => ! $filters['chi_can_nhap'] || $row['de_xuat'] > 0)
            ->sortBy([
                fn ($row) => ['khan-cap' => 0, 'can-nhap' => 1, 'on-dinh' => 2][$row['muc_uu_tien']],
                fn ($row) => $row['so_ngay_con_lai'] ?? PHP_FLOAT_MAX,
            ])
            ->values();
    }

    private function mucUuTien(float $tonKho, float $tonToiThieu, ?float $soNgayConLai, float $deXuat): array
    {
        if ($tonKho <= $tonToiThieu || ($soNgayConLai !== null && $soNgayConLai <= 2)) {
            return ['khan-cap', 'Khẩn cấp'];
        }

        if ($deXuat > 0) {
            return ['can-nhap', 'Cần nhập'];
        }

        return ['on-dinh', 'Ổn định'];
    }

    private function soLuongMua(NguyenLieu $nguyenLieu, float $deXuat): array
    {
        $tiLeSuDung = max(0.000001, (float) $nguyenLieu->ti_le_su_dung);
        $soLuongCoBan = $deXuat / $tiLeSuDung;
        $dungDonViLon = $soLuongCoBan >= 1000;
        $donViMua = $dungDonViLon
            ? ($nguyenLieu->don_vi_tinh === 'g' ? 'kg' : 'l')
            : $nguyenLieu->don_vi_tinh;
        $soLuongMua = $dungDonViLon ? $soLuongCoBan / 1000 : $soLuongCoBan;

        return [ceil($soLuongMua * 100) / 100, $donViMua];
    }
}
