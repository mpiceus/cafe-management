<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        NguoiDung::query()->firstOrCreate(
            ['ten_dang_nhap' => 'admin'],
            [
                'ho_ten' => 'Chu cua hang',
                'mat_khau' => 'admin123',
                'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        foreach (['Ca phe', 'Tra', 'Da xay', 'Topping'] as $tenLoaiMon) {
            DB::table('loai_mon')->updateOrInsert(
                ['ten_loai_mon' => $tenLoaiMon],
                ['ten_loai_mon' => $tenLoaiMon]
            );
        }

        DB::table('nha_cung_cap')->updateOrInsert(
            ['ten_nha_cung_cap' => 'Nhà cung cấp mặc định'],
            [
                'so_dien_thoai' => '0900000000',
                'email' => 'ncc@example.com',
                'dia_chi' => 'Đang cập nhật',
            ]
        );
    }
}
