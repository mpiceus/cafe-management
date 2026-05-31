<?php

namespace Tests\Feature;

use App\Models\HoaDon;
use App\Models\NguoiDung;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    public function test_owner_can_export_report_excel(): void
    {
        $this->requireTables(['hoa_don', 'nguoi_dung']);

        $owner = NguoiDung::query()->create([
            'ho_ten' => 'Chủ test',
            'ten_dang_nhap' => 'owner_report',
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);

        HoaDon::query()->create([
            'ma_nguoi_dung' => $owner->ma_nguoi_dung,
            'thoi_gian_tao' => now(),
            'tong_tien' => 45000,
            'phuong_thuc_thanh_toan' => 'tien_mat',
            'trang_thai' => HoaDon::TRANG_THAI_DA_THANH_TOAN,
            'thoi_gian_thanh_toan' => now(),
        ]);

        $response = $this->actingAs($owner)->get(route('bao-cao.export', [
            'tu_ngay' => now()->subDay()->toDateString(),
            'den_ngay' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
