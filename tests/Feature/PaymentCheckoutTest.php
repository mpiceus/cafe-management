<?php

namespace Tests\Feature;

use App\Models\HoaDon;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentCheckoutTest extends TestCase
{
    public function test_checkout_page_shows_payment_info(): void
    {
        $this->requireTables(['hoa_don', 'nguoi_dung']);

        Config::set('sepay.bank_account', '0903001234');
        Config::set('sepay.bank_code', 'MBBank');
        Config::set('sepay.bank_name', 'MBBank');
        Config::set('sepay.account_name', 'QUAN CAFE');
        Config::set('sepay.payment_prefix', 'DH');

        $owner = NguoiDung::query()->create([
            'ho_ten' => 'Chủ test',
            'ten_dang_nhap' => 'owner_checkout',
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);

        $hoaDon = HoaDon::query()->create([
            'ma_nguoi_dung' => $owner->ma_nguoi_dung,
            'thoi_gian_tao' => now(),
            'tong_tien' => 120000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'trang_thai' => HoaDon::TRANG_THAI_DANG_TAO,
        ]);

        $response = $this->actingAs($owner)->get(route('payment.checkout', $hoaDon));

        $response->assertOk();
        $response->assertSee('Thanh toán chuyển khoản');
        $response->assertSee((string) $hoaDon->ma_hoa_don);
    }

    public function test_customer_checkout_page_is_available(): void
    {
        $this->requireTables(['hoa_don', 'nguoi_dung']);

        Config::set('sepay.bank_account', '0903001234');
        Config::set('sepay.bank_code', 'MBBank');
        Config::set('sepay.bank_name', 'MBBank');
        Config::set('sepay.account_name', 'QUAN CAFE');

        $owner = NguoiDung::query()->create([
            'ho_ten' => 'Chủ test 2',
            'ten_dang_nhap' => 'owner_customer_checkout',
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);

        $hoaDon = HoaDon::query()->create([
            'ma_nguoi_dung' => $owner->ma_nguoi_dung,
            'thoi_gian_tao' => now(),
            'tong_tien' => 150000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'trang_thai' => HoaDon::TRANG_THAI_DANG_TAO,
        ]);

        $response = $this->get(route('customer.checkout', $hoaDon));

        $response->assertOk();
        $response->assertSee('Quét QR để thanh toán');
        $response->assertSee(number_format($hoaDon->tong_tien, 0, ',', '.'));
        $response->assertSee((string) $hoaDon->ma_hoa_don);
    }
}
