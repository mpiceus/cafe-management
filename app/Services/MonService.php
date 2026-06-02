<?php

namespace App\Services;

use App\Models\Mon;
use App\Repositories\Contracts\GiaMonRepositoryInterface;
use App\Repositories\Contracts\MonRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MonService
{
    public function __construct(
        private readonly MonRepositoryInterface $monRepository,
        private readonly GiaMonRepositoryInterface $giaMonRepository
    ) {
    }

    public function danhSach(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $mons = $this->monRepository->paginate($filters, $perPage);

        $mons->getCollection()->transform(function (Mon $mon) {
            $daTungBan = $mon->chiTietHoaDons()->exists() || $mon->chiTietToppings()->exists();
            $tamHetNguyenLieus = $mon->congThucs
                ->filter(fn ($congThuc) => (float) $congThuc->nguyenLieu?->ton_kho < (float) $congThuc->so_luong)
                ->map(fn ($congThuc) => $congThuc->nguyenLieu?->ten_nguyen_lieu)
                ->filter()
                ->values();

            $mon->setAttribute('tam_het', $tamHetNguyenLieus->isNotEmpty());
            $mon->setAttribute('tam_het_nguyen_lieus', $tamHetNguyenLieus);
            $mon->setAttribute('co_the_xoa', ! $daTungBan);
            $mon->setAttribute('ly_do_khong_xoa', $daTungBan ? "Món đã từng được bán; chọn 'Dừng bán' để ẩn khỏi bán." : null);

            return $mon;
        });

        return $mons;
    }

    public function loaiMons(): Collection
    {
        return $this->monRepository->loaiMons();
    }

    public function taoMoi(array $data): Mon
    {
        $this->assertUniqueTenMon($data['ten_mon']);
        $data['cho_them_topping'] = (bool) ($data['cho_them_topping'] ?? false);
        $data = $this->xuLyHinhAnh($data);

        return DB::transaction(function () use ($data) {
            $mon = $this->monRepository->create($data);

            $this->giaMonRepository->create([
                'ma_mon' => $mon->ma_mon,
                'gia' => $data['gia'],
                'ngay_ap_dung' => $data['ngay_ap_dung'] ?? now(),
            ]);

            return $mon->refresh();
        });
    }

    public function capNhat(Mon $mon, array $data): Mon
    {
        $this->assertUniqueTenMon($data['ten_mon'], $mon->ma_mon);
        $giaMoi = (float) $data['gia'];
        $data['cho_them_topping'] = (bool) ($data['cho_them_topping'] ?? false);
        $data = $this->xuLyHinhAnh($data, $mon);
        unset($data['gia'], $data['ngay_ap_dung']);

        return DB::transaction(function () use ($mon, $data, $giaMoi) {
            $mon = $this->monRepository->update($mon, $data);
            $giaHienTai = $mon->giaMoiNhat?->gia;

            if ($giaHienTai === null || (float) $giaHienTai !== $giaMoi) {
                $this->giaMonRepository->create([
                    'ma_mon' => $mon->ma_mon,
                    'gia' => $giaMoi,
                    'ngay_ap_dung' => now(),
                ]);
            }

            return $mon->refresh();
        });
    }

    public function doiTrangThai(Mon $mon): Mon
    {
        $trangThai = $mon->dangBan()
            ? Mon::TRANG_THAI_DUNG_BAN
            : Mon::TRANG_THAI_DANG_BAN;

        return $this->monRepository->update($mon, ['trang_thai' => $trangThai]);
    }

    public function xoa(Mon $mon): void
    {
        DB::transaction(function () use ($mon) {
            if ($mon->chiTietHoaDons()->exists() || $mon->chiTietToppings()->exists()) {
                throw ValidationException::withMessages([
                    'mon' => "Món đã từng được bán; chọn 'Dừng bán' để ẩn khỏi bán.",
                ]);
            }

            $mon->giaMons()->delete();
            $mon->congThucs()->delete();

            if ($mon->hinh_anh) {
                Storage::disk('public')->delete($mon->hinh_anh);
            }

            $this->monRepository->delete($mon);
        });
    }

    private function assertUniqueTenMon(string $tenMon, ?int $ignoreId = null): void
    {
        $normalized = TextNormalizer::normalize($tenMon);

        $exists = $this->monRepository->allForSearch()
            ->first(function (Mon $mon) use ($normalized, $ignoreId) {
                if ($ignoreId !== null && $mon->ma_mon === $ignoreId) {
                    return false;
                }

                return TextNormalizer::normalize($mon->ten_mon) === $normalized;
            });

        if ($exists) {
            throw ValidationException::withMessages([
                'ten_mon' => 'Tên món đã tồn tại.',
            ]);
        }
    }

    private function xuLyHinhAnh(array $data, ?Mon $mon = null): array
    {
        if (! isset($data['hinh_anh_file'])) {
            unset($data['hinh_anh_file']);

            return $data;
        }

        if ($mon?->hinh_anh) {
            Storage::disk('public')->delete($mon->hinh_anh);
        }

        $data['hinh_anh'] = $data['hinh_anh_file']->store('mon', 'public');
        unset($data['hinh_anh_file']);

        return $data;
    }
}
