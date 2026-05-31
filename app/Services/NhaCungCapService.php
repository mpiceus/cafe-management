<?php

namespace App\Services;

use App\Models\NhaCungCap;
use App\Repositories\Contracts\NhaCungCapRepositoryInterface;
use App\Support\TextNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class NhaCungCapService
{
    public function __construct(private readonly NhaCungCapRepositoryInterface $repository) {}

    public function danhSach(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function taoMoi(array $data): NhaCungCap
    {
        $this->assertUniqueTen($data['ten_nha_cung_cap']);

        return $this->repository->create($data);
    }

    public function capNhat(NhaCungCap $nhaCungCap, array $data): NhaCungCap
    {
        $this->assertUniqueTen($data['ten_nha_cung_cap'], $nhaCungCap->ma_nha_cung_cap);

        return $this->repository->update($nhaCungCap, $data);
    }

    public function xoa(NhaCungCap $nhaCungCap): void
    {
        if ($nhaCungCap->nguyenLieus()->exists() || $nhaCungCap->donNhaps()->exists()) {
            throw ValidationException::withMessages([
                'nha_cung_cap' => 'Không thể xóa nhà cung cấp đã phát sinh nguyên liệu hoặc đơn nhập.',
            ]);
        }

        $this->repository->delete($nhaCungCap);
    }

    private function assertUniqueTen(string $ten, ?int $ignoreId = null): void
    {
        $normalized = TextNormalizer::normalize($ten);

        $exists = $this->repository->allForSearch()->first(function (NhaCungCap $nhaCungCap) use ($normalized, $ignoreId) {
            if ($ignoreId !== null && $nhaCungCap->ma_nha_cung_cap === $ignoreId) {
                return false;
            }

            return TextNormalizer::normalize($nhaCungCap->ten_nha_cung_cap) === $normalized;
        });

        if ($exists) {
            throw ValidationException::withMessages([
                'ten_nha_cung_cap' => 'Tên nhà cung cấp đã tồn tại.',
            ]);
        }
    }
}
