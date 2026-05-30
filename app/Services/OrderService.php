<?php

namespace App\Services;

use App\Models\HoaDon;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly OrderRepositoryInterface $repository) {}

    public function danhSachHoaDon(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->hoaDons($filters);
    }

    public function monsDangBan(): Collection
    {
        return $this->repository->monsDangBan();
    }

    public function toppingsDangBan(): Collection
    {
        return $this->repository->toppingsDangBan();
    }

    public function nguyenLieuTuyChinh(): Collection
    {
        return $this->repository->nguyenLieuTuyChinh();
    }

    public function thanhToan(array $data, int $maNguoiDung): HoaDon
    {
        $data['items'] = collect($data['items'])
            ->filter(fn ($item) => ! empty($item['ma_mon']) && (int) ($item['so_luong'] ?? 0) > 0)
            ->map(function ($item) {
                $item['toppings'] = collect($item['toppings'] ?? [])
                    ->filter(fn ($topping) => ! empty($topping['ma_mon']) && (int) ($topping['so_luong'] ?? 0) > 0)
                    ->values()
                    ->all();

                return $item;
            })
            ->values()
            ->all();

        if (empty($data['items'])) {
            throw ValidationException::withMessages(['items' => 'Vui lòng chọn ít nhất một món.']);
        }

        return $this->repository->taoHoaDon($data['items'], $maNguoiDung, $data['phuong_thuc_thanh_toan']);
    }
}
