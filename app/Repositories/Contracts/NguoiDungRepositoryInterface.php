<?php

namespace App\Repositories\Contracts;

use App\Models\NguoiDung;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NguoiDungRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    public function allActive(): Collection;

    public function find(int $id): ?NguoiDung;

    public function findByTenDangNhap(string $tenDangNhap): ?NguoiDung;

    public function create(array $data): NguoiDung;

    public function update(NguoiDung $nguoiDung, array $data): NguoiDung;
}
