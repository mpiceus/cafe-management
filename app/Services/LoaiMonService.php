<?php

namespace App\Services;

use App\Models\LoaiMon;
use App\Repositories\Contracts\LoaiMonRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class LoaiMonService
{
    public function __construct(private readonly LoaiMonRepositoryInterface $repository) {}

    public function danhSach(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function taoMoi(array $data): LoaiMon
    {
        $this->assertUniqueTen($data['ten_loai_mon']);

        return $this->repository->create($data);
    }

    public function capNhat(LoaiMon $loaiMon, array $data): LoaiMon
    {
        $this->assertUniqueTen($data['ten_loai_mon'], $loaiMon->ma_loai_mon);

        return $this->repository->update($loaiMon, $data);
    }

    private function assertUniqueTen(string $ten, ?int $ignoreId = null): void
    {
        $normalized = TextNormalizer::normalize($ten);

        $exists = $this->repository->allForSearch()->first(function (LoaiMon $loaiMon) use ($normalized, $ignoreId) {
            if ($ignoreId !== null && $loaiMon->ma_loai_mon === $ignoreId) {
                return false;
            }

            return TextNormalizer::normalize($loaiMon->ten_loai_mon) === $normalized;
        });

        if ($exists) {
            throw ValidationException::withMessages([
                'ten_loai_mon' => 'Tên loại món đã tồn tại.',
            ]);
        }
    }
}
