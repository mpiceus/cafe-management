<?php

namespace App\Services;

use App\Models\NguyenLieu;
use App\Repositories\Contracts\NguyenLieuRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class NguyenLieuService
{
    public function __construct(private readonly NguyenLieuRepositoryInterface $repository) {}

    public function danhSach(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
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

        return $this->repository->create($data);
    }

    public function capNhat(NguyenLieu $nguyenLieu, array $data): NguyenLieu
    {
        $this->assertUniqueTen($data['ten_nguyen_lieu'], $nguyenLieu->ma_nguyen_lieu);
        $data['duoc_tuy_chinh'] = (bool) ($data['duoc_tuy_chinh'] ?? false);

        return $this->repository->update($nguyenLieu, $data);
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
