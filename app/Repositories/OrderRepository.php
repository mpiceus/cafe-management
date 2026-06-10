<?php

namespace App\Repositories;

use App\Models\ChiTietHoaDon;
use App\Models\ChiTietTopping;
use App\Models\ChiTietTuyChinh;
use App\Models\HoaDon;
use App\Models\LoaiMon;
use App\Models\Mon;
use App\Models\NguyenLieu;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRepository implements OrderRepositoryInterface
{
    public function hoaDons(array $filters = []): LengthAwarePaginator
    {
        $items = HoaDon::query()
            ->with(['chiTiets.mon', 'nguoiDung'])
            ->orderByDesc('thoi_gian_tao')
            ->get();

        if (! empty($filters['ma_hoa_don'])) {
            $keyword = trim((string) $filters['ma_hoa_don']);
            $items = $items->filter(fn (HoaDon $hoaDon) => str_contains((string) $hoaDon->ma_hoa_don, $keyword));
        }

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(fn (HoaDon $hoaDon) => $hoaDon->chiTiets->contains(
                fn ($chiTiet) => TextNormalizer::contains($chiTiet->mon?->ten_mon, $filters['tu_khoa'])
            ));
        }

        if (! empty($filters['tu_ngay'])) {
            $items = $items->filter(fn (HoaDon $hoaDon) => $hoaDon->thoi_gian_tao->greaterThanOrEqualTo($filters['tu_ngay'].' 00:00:00'));
        }

        if (! empty($filters['den_ngay'])) {
            $items = $items->filter(fn (HoaDon $hoaDon) => $hoaDon->thoi_gian_tao->lessThanOrEqualTo($filters['den_ngay'].' 23:59:59'));
        }

        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new PaginatorInstance(
            $results,
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    public function monsDangBan(): Collection
    {
        return Mon::query()
            ->with(['loaiMon', 'giaMoiNhat', 'congThucs.nguyenLieu'])
            ->where('trang_thai', Mon::TRANG_THAI_DANG_BAN)
            ->where('ma_loai_mon', '<>', LoaiMon::MA_TOPPING)
            ->get()
            ->sortBy('ten_mon')
            ->values();
    }

    public function toppingsDangBan(): Collection
    {
        return collect();
    }

    public function nguyenLieuTuyChinh(): Collection
    {
        return NguyenLieu::query()
            ->where('duoc_tuy_chinh', true)
            ->where('duoc_su_dung', true)
            ->orderBy('ten_nguyen_lieu')
            ->get();
    }

    public function taoHoaDon(array $items, int $maNguoiDung, string $phuongThuc): HoaDon
    {
        $items = $this->gopDongCauHinh($items);

        return DB::transaction(function () use ($items, $maNguoiDung, $phuongThuc) {
            $this->kiemTraGiaVaTonKho($items);

            $daThanhToan = $phuongThuc === 'tien_mat';
            $hoaDon = HoaDon::query()->create([
                'ma_nguoi_dung' => $maNguoiDung,
                'thoi_gian_tao' => now(),
                'thoi_gian_thanh_toan' => $daThanhToan ? now() : null,
                'tong_tien' => 0,
                'phuong_thuc_thanh_toan' => $phuongThuc,
                'trang_thai' => $daThanhToan ? HoaDon::TRANG_THAI_DA_THANH_TOAN : HoaDon::TRANG_THAI_DANG_TAO,
            ]);

            $tongTien = 0;
            $trangThaiPhaChe = $daThanhToan && ! $this->hasActivePhaChe()
                ? ChiTietHoaDon::PHA_CHE_DANG
                : ChiTietHoaDon::PHA_CHE_CHO;

            foreach ($items as $item) {
                $mon = Mon::query()->with(['giaMoiNhat', 'congThucs'])->findOrFail($item['ma_mon']);
                if (! $mon->giaMoiNhat) {
                    throw ValidationException::withMessages([
                        'items' => "Món {$mon->ten_mon} chưa có giá bán.",
                    ]);
                }

                $soLuong = (int) $item['so_luong'];
                $chiTiet = ChiTietHoaDon::query()->create([
                    'ma_hoa_don' => $hoaDon->ma_hoa_don,
                    'ma_mon' => $mon->ma_mon,
                    'so_luong' => $soLuong,
                    'che_do' => $item['che_do'] ?: null,
                    'ghi_chu' => $item['ghi_chu'] ?? null,
                    'trang_thai_pha_che' => $trangThaiPhaChe,
                ]);

                $tongTien += (float) $mon->giaMoiNhat->gia * $soLuong;

                foreach (($item['tuy_chinh'] ?? []) as $maNguyenLieu => $tiLe) {
                    if ((int) $tiLe !== 100) {
                        ChiTietTuyChinh::query()->create([
                            'ma_ct' => $chiTiet->ma_chi_tiet,
                            'ma_nguyen_lieu' => $maNguyenLieu,
                            'ti_le' => $tiLe,
                        ]);
                    }
                }

                foreach (($item['toppings'] ?? []) as $topping) {
                    if ((int) ($topping['so_luong'] ?? 0) <= 0) {
                        continue;
                    }

                    $toppingMon = Mon::query()->with('giaMoiNhat')->findOrFail($topping['ma_mon']);
                    ChiTietTopping::query()->create([
                        'ma_chi_tiet' => $chiTiet->ma_chi_tiet,
                        'ma_mon' => $toppingMon->ma_mon,
                        'so_luong' => $topping['so_luong'],
                    ]);
                    $tongTien += ((float) $toppingMon->giaMoiNhat?->gia ?? 0) * (int) $topping['so_luong'];
                }

                $this->truKhoMon($mon, $soLuong, $item['tuy_chinh'] ?? []);
                $this->truKhoTopping($item['toppings'] ?? []);
            }

            $hoaDon->update([
                'tong_tien' => $tongTien,
                'ma_thanh_toan' => $hoaDon->ma_thanh_toan ?: (config('sepay.payment_prefix', 'DH').$hoaDon->ma_hoa_don),
            ]);

            return $hoaDon->load(['chiTiets.mon', 'nguoiDung']);
        });
    }

    private function hasActivePhaChe(): bool
    {
        return ChiTietHoaDon::query()
            ->where('trang_thai_pha_che', ChiTietHoaDon::PHA_CHE_DANG)
            ->exists();
    }

    private function truKhoMon(Mon $mon, int $soLuong, array $tuyChinh): void
    {
        foreach ($mon->congThucs as $congThuc) {
            $tiLe = ((int) ($tuyChinh[$congThuc->ma_nguyen_lieu] ?? 100)) / 100;
            $canDung = (float) $congThuc->so_luong * $soLuong * $tiLe;
            $nguyenLieu = NguyenLieu::query()->lockForUpdate()->findOrFail($congThuc->ma_nguyen_lieu);

            if ((float) $nguyenLieu->ton_kho < $canDung) {
                throw ValidationException::withMessages([
                    'items' => "Không đủ nguyên liệu {$nguyenLieu->ten_nguyen_lieu}.",
                ]);
            }

            $nguyenLieu->decrement('ton_kho', $canDung);
        }
    }

    private function truKhoTopping(array $toppings): void
    {
        foreach ($toppings as $topping) {
            $mon = Mon::query()->with('congThucs')->findOrFail($topping['ma_mon']);

            foreach ($mon->congThucs as $congThuc) {
                $canDung = (float) $congThuc->so_luong * (int) $topping['so_luong'];
                $nguyenLieu = NguyenLieu::query()->lockForUpdate()->findOrFail($congThuc->ma_nguyen_lieu);

                if ((float) $nguyenLieu->ton_kho < $canDung) {
                    throw ValidationException::withMessages([
                        'items' => "Không đủ nguyên liệu {$nguyenLieu->ten_nguyen_lieu} cho topping.",
                    ]);
                }

                $nguyenLieu->decrement('ton_kho', $canDung);
            }
        }
    }

    private function kiemTraGiaVaTonKho(array $items): void
    {
        $usage = [];

        foreach ($items as $item) {
            $mon = Mon::query()->with(['giaMoiNhat', 'congThucs'])->findOrFail($item['ma_mon']);
            if (! $mon->giaMoiNhat) {
                throw ValidationException::withMessages([
                    'items' => "Món {$mon->ten_mon} chưa có giá bán.",
                ]);
            }

            $soLuong = (int) $item['so_luong'];
            foreach ($mon->congThucs as $congThuc) {
                $tiLe = ((int) (($item['tuy_chinh'] ?? [])[$congThuc->ma_nguyen_lieu] ?? 100)) / 100;
                $usage[$congThuc->ma_nguyen_lieu] = ($usage[$congThuc->ma_nguyen_lieu] ?? 0)
                    + ((float) $congThuc->so_luong * $soLuong * $tiLe);
            }

            foreach (($item['toppings'] ?? []) as $topping) {
                if ((int) ($topping['so_luong'] ?? 0) <= 0) {
                    continue;
                }

                $toppingMon = Mon::query()->with('congThucs')->findOrFail($topping['ma_mon']);
                foreach ($toppingMon->congThucs as $congThuc) {
                    $usage[$congThuc->ma_nguyen_lieu] = ($usage[$congThuc->ma_nguyen_lieu] ?? 0)
                        + ((float) $congThuc->so_luong * (int) $topping['so_luong']);
                }
            }
        }

        if (empty($usage)) {
            return;
        }

        $nguyenLieus = NguyenLieu::query()
            ->lockForUpdate()
            ->whereIn('ma_nguyen_lieu', array_keys($usage))
            ->get()
            ->keyBy('ma_nguyen_lieu');

        foreach ($usage as $maNguyenLieu => $canDung) {
            $nguyenLieu = $nguyenLieus->get($maNguyenLieu);
            if (! $nguyenLieu || (float) $nguyenLieu->ton_kho < (float) $canDung) {
                throw ValidationException::withMessages([
                    'items' => 'Không đủ nguyên liệu '.($nguyenLieu?->ten_nguyen_lieu ?? "#{$maNguyenLieu}").'.',
                ]);
            }
        }
    }

    private function gopDongCauHinh(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $signature = json_encode([
                'ma_mon' => $item['ma_mon'],
                'che_do' => $item['che_do'] ?? '',
                'ghi_chu' => trim((string) ($item['ghi_chu'] ?? '')),
                'tuy_chinh' => collect($item['tuy_chinh'] ?? [])->filter()->sortKeys()->all(),
                'toppings' => collect($item['toppings'] ?? [])
                    ->map(fn ($topping) => [
                        'ma_mon' => (int) $topping['ma_mon'],
                        'so_luong' => (int) $topping['so_luong'],
                    ])
                    ->sortBy('ma_mon')
                    ->values()
                    ->all(),
            ]);

            if (! isset($grouped[$signature])) {
                $grouped[$signature] = $item;
                continue;
            }

            $grouped[$signature]['so_luong'] += (int) $item['so_luong'];
        }

        return array_values($grouped);
    }
}
