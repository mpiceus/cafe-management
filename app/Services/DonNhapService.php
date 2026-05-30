<?php

namespace App\Services;

use App\Models\DonNhap;
use App\Models\NguyenLieu;
use App\Repositories\Contracts\DonNhapRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DonNhapService
{
    public function __construct(private readonly DonNhapRepositoryInterface $repository) {}

    public function danhSach(array $filters = []): LengthAwarePaginator { return $this->repository->paginate($filters); }
    public function chiTiet(int $maDonNhap): DonNhap
    {
        return $this->repository->findById($maDonNhap)
            ?? throw new ModelNotFoundException();
    }

    public function nhaCungCaps(): Collection { return $this->repository->nhaCungCaps(); }
    public function nguyenLieus(): Collection { return $this->repository->nguyenLieus(); }

    public function taoDonNhap(array $data, int $maNguoiDung): DonNhap
    {
        $nguyenLieus = $this->repository->nguyenLieus()->keyBy('ma_nguyen_lieu');

        $items = collect($data['items'] ?? [])
            ->filter(fn ($item) => ! empty($item['ma_nguyen_lieu']) && (float) ($item['so_luong_mua'] ?? 0) > 0)
            ->map(function ($item) use ($nguyenLieus, $data) {
                /** @var NguyenLieu|null $nguyenLieu */
                $nguyenLieu = $nguyenLieus->get((int) $item['ma_nguyen_lieu']);
                if (! $nguyenLieu) {
                    return null;
                }

                if ((int) $nguyenLieu->ma_nha_cung_cap !== (int) $data['ma_nha_cung_cap']) {
                    throw ValidationException::withMessages([
                        'items' => 'Có nguyên liệu không thuộc nhà cung cấp đã chọn.',
                    ]);
                }

                $donViHopLe = $nguyenLieu->don_vi_tinh === 'g' ? ['g', 'kg'] : ['ml', 'l'];
                if (! in_array($item['don_vi_mua'], $donViHopLe, true)) {
                    throw ValidationException::withMessages([
                        'items' => "Đơn vị mua của {$nguyenLieu->ten_nguyen_lieu} không hợp lệ.",
                    ]);
                }

                $heSoDonVi = in_array($item['don_vi_mua'], ['kg', 'l'], true) ? 1000 : 1;
                $soLuongCoBan = (float) $item['so_luong_mua'] * $heSoDonVi;
                $soLuongNhapKho = $soLuongCoBan * (float) $nguyenLieu->ti_le_su_dung;
                $thanhTien = ($soLuongCoBan / 100) * (float) $item['don_gia'];

                return [
                    'ma_nguyen_lieu' => (int) $item['ma_nguyen_lieu'],
                    'so_luong_mua' => (float) $item['so_luong_mua'],
                    'don_vi_mua' => $item['don_vi_mua'],
                    'so_luong_nhap_kho' => $soLuongNhapKho,
                    'don_gia' => (float) $item['don_gia'],
                    'thanh_tien' => $thanhTien,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($items)) {
            throw ValidationException::withMessages(['items' => 'Vui lòng nhập ít nhất một nguyên liệu.']);
        }

        return $this->repository->createWithDetails([
            'ma_nha_cung_cap' => $data['ma_nha_cung_cap'],
            'ma_nguoi_dung' => $maNguoiDung,
        ], $items);
    }
}
