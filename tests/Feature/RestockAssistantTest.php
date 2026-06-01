<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use App\Models\NguyenLieu;
use App\Models\NhaCungCap;
use Tests\TestCase;

class RestockAssistantTest extends TestCase
{
    public function test_owner_sees_rule_based_restock_suggestion_for_low_stock_ingredient(): void
    {
        $this->requireTables(['nguoi_dung', 'nha_cung_cap', 'nguyen_lieu']);

        $owner = $this->owner();
        $supplier = $this->supplier();
        $ingredient = NguyenLieu::query()->create([
            'ten_nguyen_lieu' => 'Nguyen lieu goi y '.uniqid(),
            'don_vi_tinh' => 'g',
            'ton_kho' => 100,
            'so_luong_toi_thieu' => 500,
            'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
            'ti_le_su_dung' => 1,
            'duoc_tuy_chinh' => false,
        ]);

        $this->actingAs($owner)
            ->get(route('bao-cao.index'))
            ->assertOk()
            ->assertSee('Trợ lý nhập kho')
            ->assertSee($ingredient->ten_nguyen_lieu)
            ->assertSee('Khẩn cấp');
    }

    public function test_ingredient_at_minimum_stock_is_still_suggested_without_usage_history(): void
    {
        $this->requireTables(['nguoi_dung', 'nha_cung_cap', 'nguyen_lieu']);

        $owner = $this->owner();
        $supplier = $this->supplier();
        $ingredient = NguyenLieu::query()->create([
            'ten_nguyen_lieu' => 'Nguyen lieu cham nguong '.uniqid(),
            'don_vi_tinh' => 'g',
            'ton_kho' => 5000,
            'so_luong_toi_thieu' => 5000,
            'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
            'ti_le_su_dung' => 1,
            'duoc_tuy_chinh' => false,
        ]);

        $this->actingAs($owner)
            ->get(route('bao-cao.index', ['chi_can_nhap' => 1]))
            ->assertOk()
            ->assertSee($ingredient->ten_nguyen_lieu)
            ->assertSee('Khẩn cấp')
            ->assertSee('5.000 g');
    }

    public function test_purchase_order_create_page_accepts_valid_restock_prefill(): void
    {
        $this->requireTables(['nguoi_dung', 'nha_cung_cap', 'nguyen_lieu']);

        $owner = $this->owner();
        $supplier = $this->supplier();
        $ingredient = NguyenLieu::query()->create([
            'ten_nguyen_lieu' => 'Nguyen lieu prefill '.uniqid(),
            'don_vi_tinh' => 'g',
            'ton_kho' => 0,
            'so_luong_toi_thieu' => 1000,
            'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
            'ti_le_su_dung' => 1,
            'duoc_tuy_chinh' => false,
        ]);

        $this->actingAs($owner)
            ->get(route('don-nhap.create', [
                'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
                'items' => [[
                    'ma_nguyen_lieu' => $ingredient->ma_nguyen_lieu,
                    'so_luong_mua' => 1.5,
                    'don_vi_mua' => 'kg',
                ]],
            ]))
            ->assertOk()
            ->assertSee('value="'.$supplier->ma_nha_cung_cap.'" selected', false)
            ->assertSee('"ma_nguyen_lieu":'.$ingredient->ma_nguyen_lieu, false)
            ->assertSee('"so_luong_mua":1.5', false)
            ->assertSee('"don_vi_mua":"kg"', false);
    }

    private function owner(): NguoiDung
    {
        return NguoiDung::query()->create([
            'ho_ten' => 'Chu cua hang kiem thu',
            'ten_dang_nhap' => 'restock_owner_'.uniqid(),
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);
    }

    private function supplier(): NhaCungCap
    {
        return NhaCungCap::query()->create([
            'ten_nha_cung_cap' => 'Nha cung cap kiem thu '.uniqid(),
        ]);
    }
}
