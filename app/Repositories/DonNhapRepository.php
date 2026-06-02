<?php

namespace App\Repositories;

use App\Models\ChiTietDonNhap;
use App\Models\DonNhap;
use App\Models\NguyenLieu;
use App\Models\NhaCungCap;
use App\Repositories\Contracts\DonNhapRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonNhapRepository implements DonNhapRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $items = DonNhap::query()
            ->with(['nhaCungCap', 'nguoiDung', 'chiTiets.nguyenLieu'])
            ->orderByDesc('ngay_nhap')
            ->get();

        if (! empty($filters['tu_khoa'])) {
            $items = $items->filter(function (DonNhap $donNhap) use ($filters) {
                if (TextNormalizer::contains($donNhap->nhaCungCap?->ten_nha_cung_cap, $filters['tu_khoa'])) {
                    return true;
                }

                if (TextNormalizer::contains($donNhap->nguoiDung?->ho_ten, $filters['tu_khoa'])) {
                    return true;
                }

                return $donNhap->chiTiets->contains(fn ($chiTiet) => TextNormalizer::contains($chiTiet->nguyenLieu?->ten_nguyen_lieu, $filters['tu_khoa']));
            });
        }

        $page = Paginator::resolveCurrentPage() ?: 1;
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new PaginatorInstance(
            $results,
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    public function findById(int $maDonNhap): ?DonNhap
    {
        return DonNhap::query()
            ->with(['nhaCungCap', 'nguoiDung', 'chiTiets.nguyenLieu'])
            ->find($maDonNhap);
    }

    public function nhaCungCaps(): Collection
    {
        return NhaCungCap::query()->orderBy('ten_nha_cung_cap')->get();
    }

    public function nguyenLieus(): Collection
    {
        return NguyenLieu::query()
            ->with('nhaCungCap')
            ->where('duoc_su_dung', true)
            ->orderBy('ten_nguyen_lieu')
            ->get();
    }

    public function createWithDetails(array $data, array $items): DonNhap
    {
        return DB::transaction(function () use ($data, $items) {
            $tongTien = collect($items)->sum(fn ($item) => (float) $item['thanh_tien']);
            $donNhap = DonNhap::query()->create($data + ['tong_tien' => $tongTien, 'ngay_nhap' => now()]);

            foreach ($items as $item) {
                $nguyenLieu = NguyenLieu::query()->lockForUpdate()->findOrFail($item['ma_nguyen_lieu']);

                if ((int) $nguyenLieu->ma_nha_cung_cap !== (int) $data['ma_nha_cung_cap']) {
                    throw ValidationException::withMessages([
                        'items' => 'Có nguyên liệu không thuộc nhà cung cấp đã chọn.',
                    ]);
                }

                ChiTietDonNhap::query()->create([
                    'ma_don_nhap' => $donNhap->ma_don_nhap,
                    'ma_nguyen_lieu' => $item['ma_nguyen_lieu'],
                    'so_luong' => $item['so_luong_mua'],
                    'don_vi_mua' => $item['don_vi_mua'],
                    'so_luong_nhap_kho' => $item['so_luong_nhap_kho'],
                    'don_gia' => $item['don_gia'],
                ]);

                $nguyenLieu->increment('ton_kho', $item['so_luong_nhap_kho']);
            }

            return $donNhap->load(['nhaCungCap', 'chiTiets.nguyenLieu']);
        });
    }
}
