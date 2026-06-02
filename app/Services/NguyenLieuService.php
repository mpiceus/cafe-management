<?php

namespace App\Services;

use App\Models\NguyenLieu;
use App\Repositories\Contracts\NguyenLieuRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NguyenLieuService
{
    public function __construct(private readonly NguyenLieuRepositoryInterface $repository) {}

    public function danhSach(array $filters = []): LengthAwarePaginator
    {
        $nguyenLieus = $this->repository->paginate($filters);

        $nguyenLieus->getCollection()->transform(function (NguyenLieu $nguyenLieu) {
            $dangDuocDung = $this->dangDuocThamChieu($nguyenLieu);
            $nguyenLieu->setAttribute('co_the_xoa', ! $dangDuocDung);
            $nguyenLieu->setAttribute('ly_do_khong_xoa', $dangDuocDung ? "Nguyên liệu đã phát sinh dữ liệu; chọn 'Ngừng sử dụng' để ẩn khỏi quy trình mới." : null);

            return $nguyenLieu;
        });

        return $nguyenLieus;
    }

    public function nhaCungCaps(): Collection
    {
        return $this->repository->nhaCungCaps();
    }

    public function tatCa(): Collection
    {
        return $this->repository->all();
    }

    public function taoMoi(array $data): NguyenLieu
    {
        $this->assertUniqueTen($data['ten_nguyen_lieu']);
        $data['duoc_tuy_chinh'] = (bool) ($data['duoc_tuy_chinh'] ?? false);
        $data['duoc_su_dung'] = (bool) ($data['duoc_su_dung'] ?? true);

        return $this->repository->create($data);
    }

    public function capNhat(NguyenLieu $nguyenLieu, array $data): NguyenLieu
    {
        $this->assertUniqueTen($data['ten_nguyen_lieu'], $nguyenLieu->ma_nguyen_lieu);
        $data['duoc_tuy_chinh'] = (bool) ($data['duoc_tuy_chinh'] ?? false);
        $data['duoc_su_dung'] = (bool) ($data['duoc_su_dung'] ?? false);

        return $this->repository->update($nguyenLieu, $data);
    }

    public function xoa(NguyenLieu $nguyenLieu): void
    {
        DB::transaction(function () use ($nguyenLieu) {
            if ($this->dangDuocThamChieu($nguyenLieu)) {
                throw ValidationException::withMessages([
                    'nguyen_lieu' => "Nguyên liệu đã phát sinh dữ liệu; chọn 'Ngừng sử dụng' để ẩn khỏi quy trình mới.",
                ]);
            }

            $this->repository->delete($nguyenLieu);
        });
    }

    public function ngungSuDung(NguyenLieu $nguyenLieu): NguyenLieu
    {
        return $this->repository->update($nguyenLieu, [
            'so_luong_toi_thieu' => 0,
            'duoc_tuy_chinh' => false,
            'duoc_su_dung' => false,
        ]);
    }

    private function dangDuocThamChieu(NguyenLieu $nguyenLieu): bool
    {
        return $nguyenLieu->congThucs()->exists()
            || $nguyenLieu->chiTietDonNhaps()->exists()
            || $nguyenLieu->chiTietTuyChinhs()->exists();
    }

    private function assertUniqueTen(string $ten, ?int $ignoreId = null): void
    {
        $normalized = TextNormalizer::normalize($ten);

        $exists = $this->repository->allForSearch()->first(function (NguyenLieu $item) use ($normalized, $ignoreId) {
            if ($ignoreId !== null && $item->ma_nguyen_lieu === $ignoreId) {
                return false;
            }

            return TextNormalizer::normalize($item->ten_nguyen_lieu) === $normalized;
        });

        if ($exists) {
            throw ValidationException::withMessages([
                'ten_nguyen_lieu' => 'Tên nguyên liệu đã tồn tại.',
            ]);
        }
    }
}
