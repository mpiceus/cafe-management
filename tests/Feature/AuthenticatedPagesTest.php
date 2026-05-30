<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use Tests\TestCase;

class AuthenticatedPagesTest extends TestCase
{
    public function test_owner_can_open_main_management_pages(): void
    {
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

        foreach (['/mon', '/gia-mon', '/nguyen-lieu', '/don-nhap', '/cong-thuc', '/order', '/order/tao-moi', '/pha-che', '/bao-cao'] as $uri) {
            $this->actingAs($owner)->get($uri)->assertOk();
        }
    }
}
