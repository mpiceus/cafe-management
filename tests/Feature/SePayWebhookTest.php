<?php

namespace Tests\Feature;

use App\Models\HoaDon;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SePayWebhookTest extends TestCase
{
    public function test_webhook_marks_order_paid_and_records_transaction(): void
    {
        $this->requireTables(['hoa_don', 'nguoi_dung', 'sepay_transactions']);

        Config::set('sepay.webhook_key', 'test-key');
        Config::set('sepay.payment_prefix', 'DH');

        $owner = NguoiDung::query()->create([
            'ho_ten' => 'Chủ test',
            'ten_dang_nhap' => 'owner_webhook',
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);

        $hoaDon = HoaDon::query()->create([
            'ma_nguoi_dung' => $owner->ma_nguoi_dung,
            'thoi_gian_tao' => now(),
            'tong_tien' => 100000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'trang_thai' => HoaDon::TRANG_THAI_DANG_TAO,
            'ma_thanh_toan' => 'DH'.$owner->ma_nguoi_dung.'-'.$owner->ma_nguoi_dung,
        ]);

        $payload = [
            'id' => 'TX-1001',
            'gateway' => 'MBBank',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber' => '0123456789',
            'transferType' => 'in',
            'transferAmount' => 100000,
            'accumulated' => 200000,
            'content' => 'DH'.$hoaDon->ma_hoa_don,
            'referenceCode' => 'MBVCB.123',
            'description' => 'test',
        ];

        $response = $this->postJson(route('sepay.webhook'), $payload, [
            'Authorization' => 'Apikey test-key',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $hoaDon->refresh();
        $this->assertSame(HoaDon::TRANG_THAI_DA_THANH_TOAN, $hoaDon->trang_thai);
        $this->assertNotNull($hoaDon->thoi_gian_thanh_toan);

        $this->assertDatabaseHas('sepay_transactions', [
            'sepay_id' => 'TX-1001',
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
        ]);
    }

    public function test_webhook_can_resolve_order_id_from_code_field(): void
    {
        $this->requireTables(['hoa_don', 'nguoi_dung', 'sepay_transactions']);

        Config::set('sepay.webhook_key', 'test-key');
        Config::set('sepay.payment_prefix', 'DH');

        $owner = NguoiDung::query()->create([
            'ho_ten' => 'Chủ test code',
            'ten_dang_nhap' => 'owner_webhook_code',
            'mat_khau' => 'password',
            'chuc_vu' => NguoiDung::CHUC_VU_CHU_CUA_HANG,
            'trang_thai' => NguoiDung::TRANG_THAI_HOAT_DONG,
        ]);

        $hoaDon = HoaDon::query()->create([
            'ma_nguoi_dung' => $owner->ma_nguoi_dung,
            'thoi_gian_tao' => now(),
            'tong_tien' => 40000,
            'phuong_thuc_thanh_toan' => 'chuyen_khoan',
            'trang_thai' => HoaDon::TRANG_THAI_DANG_TAO,
        ]);

        $payload = [
            'id' => 'TX-1002',
            'gateway' => 'Vietcombank',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber' => '0000000001',
            'subAccount' => 'SBSEPAY13KXONW8Q2LM',
            'code' => 'DH'.$hoaDon->ma_hoa_don,
            'content' => 'Thanh toan don hang DH'.$hoaDon->ma_hoa_don,
            'transferType' => 'in',
            'transferAmount' => 40000,
            'referenceCode' => 'SBCE261BE18599',
            'accumulated' => 0,
            'description' => 'Thanh toan don hang DH'.$hoaDon->ma_hoa_don,
        ];

        $response = $this->postJson(route('sepay.webhook'), $payload, [
            'Authorization' => 'Apikey test-key',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $hoaDon->refresh();
        $this->assertSame(HoaDon::TRANG_THAI_DA_THANH_TOAN, $hoaDon->trang_thai);
        $this->assertDatabaseHas('sepay_transactions', [
            'sepay_id' => 'TX-1002',
            'ma_hoa_don' => $hoaDon->ma_hoa_don,
        ]);
    }
}
