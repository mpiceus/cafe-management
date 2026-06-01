<?php

namespace Tests\Feature;

use App\Models\LoaiMon;
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
                'ho_ten' => 'Chu kiem thu',
                'mat_khau' => 'password',
                'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        $this->actingAs($owner)->get('/')->assertRedirect('/bao-cao');

        foreach (['/loai-mon', '/nha-cung-cap', '/mon', '/gia-mon', '/nguyen-lieu', '/don-nhap', '/cong-thuc', '/order', '/order/tao-moi', '/pha-che', '/bao-cao'] as $uri) {
            $this->actingAs($owner)->get($uri)->assertOk();
        }

        $this->actingAs($owner)->get('/bao-cao')
            ->assertSee('Xu hướng doanh thu')
            ->assertSee('Gợi ý nhập kho')
            ->assertSee('report-revenue-chart');
    }

    public function test_order_staff_can_open_read_only_catalog_recipe_and_nhap_hang_pages(): void
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

        foreach (['/loai-mon', '/nha-cung-cap', '/mon', '/nguyen-lieu', '/cong-thuc', '/don-nhap', '/don-nhap/create'] as $uri) {
            $this->actingAs($staff)->get($uri)->assertOk();
        }

        foreach (['/loai-mon/create', '/nha-cung-cap/create', '/mon/create', '/nguyen-lieu/create'] as $uri) {
            $this->actingAs($staff)->get($uri)->assertForbidden();
        }

        $this->actingAs($staff)->get('/cong-thuc/create')->assertNotFound();
    }

    public function test_barista_can_open_read_only_catalog_recipe_nhap_hang_and_pha_che_pages(): void
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

        foreach (['/loai-mon', '/nha-cung-cap', '/mon', '/nguyen-lieu', '/cong-thuc', '/don-nhap', '/don-nhap/create', '/pha-che'] as $uri) {
            $this->actingAs($barista)->get($uri)->assertOk();
        }

        foreach (['/loai-mon/create', '/nha-cung-cap/create', '/mon/create', '/nguyen-lieu/create'] as $uri) {
            $this->actingAs($barista)->get($uri)->assertForbidden();
        }

        $this->actingAs($barista)->get('/cong-thuc/create')->assertNotFound();
    }

    public function test_bootstrap_pagination_is_rendered_on_paginated_tabs(): void
    {
        $this->requireTables(['nguoi_dung', 'loai_mon']);

        $owner = NguoiDung::query()->firstOrCreate(
            ['ten_dang_nhap' => 'test_owner_pagination'],
            [
                'ho_ten' => 'Chu kiem thu phan trang',
                'mat_khau' => 'password',
                'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
                'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
            ]
        );

        for ($index = 1; $index <= 11; $index++) {
            LoaiMon::query()->firstOrCreate(
                ['ten_loai_mon' => 'Loai test '.$index],
                []
            );
        }

        $response = $this->actingAs($owner)->get('/loai-mon');

        $response->assertOk();
        $response->assertSee('pagination');
        $response->assertSee('page-link');
    }
}
