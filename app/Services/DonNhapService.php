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

    public function duLieuTaoDonNhap(array $data): array
    {
        $maNhaCungCap = filled($data['ma_nha_cung_cap'] ?? null)
            ? (int) $data['ma_nha_cung_cap']
            : null;
        $nguyenLieus = $this->repository->nguyenLieus()->keyBy('ma_nguyen_lieu');

        $items = collect($data['items'] ?? [])
            ->map(function ($item) use ($nguyenLieus, $maNhaCungCap) {
                /** @var NguyenLieu|null $nguyenLieu */
                $nguyenLieu = $nguyenLieus->get((int) ($item['ma_nguyen_lieu'] ?? 0));
                $soLuongMua = (float) ($item['so_luong_mua'] ?? 0);
                $donViMua = (string) ($item['don_vi_mua'] ?? '');

                if (! $nguyenLieu
                    || ! $maNhaCungCap
                    || (int) $nguyenLieu->ma_nha_cung_cap !== $maNhaCungCap
                    || $soLuongMua <= 0
                    || ! in_array($donViMua, $this->donViMuaHopLe($nguyenLieu), true)) {
                    return null;
                }

                return [
                    'ma_nguyen_lieu' => (int) $nguyenLieu->ma_nguyen_lieu,
                    'so_luong_mua' => $soLuongMua,
                    'don_vi_mua' => $donViMua,
                    'don_gia' => '',
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'prefillSupplierId' => $maNhaCungCap,
            'prefillItems' => $items,
        ];
    }

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

                $donViHopLe = $this->donViMuaHopLe($nguyenLieu);
                if (! in_array($item['don_vi_mua'], $donViHopLe, true)) {
                    throw ValidationException::withMessages([
                        'items' => "Đơn vị mua của {$nguyenLieu->ten_nguyen_lieu} không hợp lệ.",
                    ]);
                }

                $heSoDonVi = $this->heSoDonVi($item['don_vi_mua']);
                $soLuongCoBan = (float) $item['so_luong_mua'] * $heSoDonVi;
                $soLuongNhapKho = $soLuongCoBan * (float) $nguyenLieu->ti_le_su_dung;
                $thanhTien = $this->thanhTien(
                    (float) $item['so_luong_mua'],
                    $item['don_vi_mua'],
                    (float) $item['don_gia']
                );

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

    private function heSoDonVi(string $donViMua): int
    {
        return in_array($donViMua, ['kg', 'l'], true) ? 1000 : 1;
    }

    private function donViMuaHopLe(NguyenLieu $nguyenLieu): array
    {
        return $nguyenLieu->don_vi_tinh === 'g' ? ['g', 'kg'] : ['ml', 'l'];
    }

    private function thanhTien(float $soLuongMua, string $donViMua, float $donGia): float
    {
        if (in_array($donViMua, ['kg', 'l'], true)) {
            return $soLuongMua * $donGia;
        }

        return ($soLuongMua / 100) * $donGia;
    }
}
