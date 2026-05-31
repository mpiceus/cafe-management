<?php

namespace Tests\Unit;

use App\Models\HoaDon;
use App\Services\SePayService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SePayServiceTest extends TestCase
{
    protected bool $useTransactions = false;

    public function test_extract_order_id_from_content(): void
    {
        Config::set('sepay.payment_prefix', 'DH');
        $service = new SePayService();

        $this->assertSame(123, $service->extractOrderId('Thanh toan DH123'));
        $this->assertNull($service->extractOrderId('Khong co ma'));
    }

    public function test_qr_url_contains_payment_data(): void
    {
        Config::set('sepay.bank_account', '0903001234');
        Config::set('sepay.bank_code', 'MBBank');
        Config::set('sepay.payment_prefix', 'DH');
        Config::set('sepay.qr_template', 'compact');

        $hoaDon = new HoaDon([
            'ma_hoa_don' => 10,
            'tong_tien' => 150000,
            'ma_thanh_toan' => 'DH10',
        ]);

        $service = new SePayService();
        $url = $service->qrUrl($hoaDon);

        $this->assertStringContainsString('qr.sepay.vn', $url);
        $this->assertStringContainsString('amount=150000', $url);
        $this->assertStringContainsString('des=DH10', $url);
    }
}
