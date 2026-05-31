<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use Tests\TestCase;

class AuthenticatedPagesTest extends TestCase
{
    public function test_owner_can_open_main_management_pages(): void
    {
        $this->requireTables(['nguoi_dung']);

        $owner = NguoiDung::query()->firstOrCreate(
            ['ten_dang_nhap' => 'test_owner'],
            [
                'ho_ten' => 'Chủ kiểm thử',
                'mat_khau' => 'password',
                'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        $this->actingAs($owner)->get('/')->assertRedirect('/bao-cao');

        foreach (['/loai-mon', '/nha-cung-cap', '/mon', '/gia-mon', '/nguyen-lieu', '/don-nhap', '/cong-thuc', '/order', '/order/tao-moi', '/pha-che', '/bao-cao'] as $uri) {
            $this->actingAs($owner)->get($uri)->assertOk();
        }
    }

    public function test_order_staff_can_open_read_only_mon_and_recipe_and_nhap_hang_pages(): void
    {
        $this->requireTables(['nguoi_dung']);

        $staff = NguoiDung::query()->firstOrCreate(
            ['ten_dang_nhap' => 'test_order_staff'],
            [
                'ho_ten' => 'Order Staff',
                'mat_khau' => 'password',
                'chuc_vu' => NguoiDung::CHUC_VU_NHAN_VIEN_ORDER,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        $this->actingAs($staff)->get('/')->assertRedirect(route('order.index'));
        $this->actingAs($staff)->get('/mon')->assertOk()->assertSee('Quản lý món')->assertDontSee('Thêm món');
        $this->actingAs($staff)->get('/cong-thuc')->assertOk()->assertSee('Công thức pha chế');
        $this->actingAs($staff)->get('/don-nhap')->assertOk();
        $this->actingAs($staff)->get('/mon/create')->assertForbidden();
        $this->actingAs($staff)->get('/cong-thuc/create')->assertStatus(405);
    }

    public function test_barista_can_open_read_only_mon_recipe_and_pha_che_pages_without_dashboard_tab(): void
    {
        $this->requireTables(['nguoi_dung']);

        $barista = NguoiDung::query()->firstOrCreate(
            ['ten_dang_nhap' => 'test_barista_staff'],
            [
                'ho_ten' => 'Barista Staff',
                'mat_khau' => 'password',
                'chuc_vu' => NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        $this->actingAs($barista)->get('/mon')->assertOk()->assertDontSee('Thêm món');
        $this->actingAs($barista)->get('/cong-thuc')->assertOk();
        $this->actingAs($barista)->get('/pha-che')->assertOk()->assertDontSee('Trang chủ');
        $this->actingAs($barista)->get('/mon/create')->assertForbidden();
        $this->actingAs($barista)->get('/cong-thuc/create')->assertStatus(405);
    }
}
