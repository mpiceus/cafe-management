<?php

namespace App\Repositories;

use App\Models\NguoiDung;
use App\Repositories\Contracts\NguoiDungRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NguoiDungRepository implements NguoiDungRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return NguoiDung::query()
            ->orderByDesc('ma_nguoi_dung')
            ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return NguoiDung::query()
            ->where('trang_thai', NguoiDung::TRANG_THAI_HOAT_DONG)
            ->orderBy('ho_ten')
            ->get();
    }

    public function find(int $id): ?NguoiDung
    {
        return NguoiDung::query()->find($id);
    }

    public function findByTenDangNhap(string $tenDangNhap): ?NguoiDung
    {
        return NguoiDung::query()
            ->where('ten_dang_nhap', $tenDangNhap)
            ->first();
    }

    public function create(array $data): NguoiDung
    {
        return NguoiDung::query()->create($data);
    }

    public function update(NguoiDung $nguoiDung, array $data): NguoiDung
    {
        $nguoiDung->fill($data);
        $nguoiDung->save();

        return $nguoiDung->refresh();
    }
}
