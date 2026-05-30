<?php

namespace App\Services;

use App\Models\NguoiDung;
use App\Repositories\Contracts\NguoiDungRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class NguoiDungService
{
    public function __construct(
        private readonly NguoiDungRepositoryInterface $nguoiDungRepository
    ) {
    }

    public function danhSach(int $perPage = 10): LengthAwarePaginator
    {
        return $this->nguoiDungRepository->paginate($perPage);
    }

    public function dangNhap(array $credentials): void
    {
        $nguoiDung = $this->nguoiDungRepository->findByTenDangNhap($credentials['ten_dang_nhap']);

        if (! $nguoiDung || ! Auth::attempt([
            'ten_dang_nhap' => $credentials['ten_dang_nhap'],
            'password' => $credentials['mat_khau'],
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ])) {
            throw ValidationException::withMessages([
                'ten_dang_nhap' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
            ]);
        }
    }

    public function taoMoi(array $data): NguoiDung
    {
        return $this->nguoiDungRepository->create($data);
    }

    public function capNhat(NguoiDung $nguoiDung, array $data): NguoiDung
    {
        if (blank($data['mat_khau'] ?? null)) {
            $data = Arr::except($data, ['mat_khau']);
        }

        return $this->nguoiDungRepository->update($nguoiDung, $data);
    }

    public function doiTrangThai(NguoiDung $nguoiDung, string $trangThai): NguoiDung
    {
        return $this->nguoiDungRepository->update($nguoiDung, [
            'trang_thai' => $trangThai,
        ]);
    }
}
