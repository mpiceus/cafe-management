<?php

namespace App\Services;

use App\Models\HoaDon;
use App\Models\SePayTransaction;
use Illuminate\Http\Request;

class SePayService
{
    public function paymentCode(HoaDon $hoaDon): string
    {
        if (! empty($hoaDon->ma_thanh_toan)) {
            return (string) $hoaDon->ma_thanh_toan;
        }

        return config('sepay.payment_prefix', 'DH').$hoaDon->ma_hoa_don;
    }

    public function qrUrl(HoaDon $hoaDon): string
    {
        $query = http_build_query([
            'acc' => config('sepay.bank_account'),
            'bank' => config('sepay.bank_code'),
            'amount' => (int) round((float) $hoaDon->tong_tien),
            'des' => $this->paymentCode($hoaDon),
            'template' => config('sepay.qr_template', 'compact'),
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://qr.sepay.vn/img?'.$query;
    }

    public function verifyWebhook(Request $request): bool
    {
        $expected = 'Apikey '.config('sepay.webhook_key');
        $provided = (string) $request->header('Authorization', '');

        if (empty($expected) || empty($provided)) {
            return false;
        }

        return hash_equals($expected, $provided);
    }

    public function extractOrderId(string $content): ?int
    {
        $prefix = preg_quote((string) config('sepay.payment_prefix', 'DH'), '/');
        $pattern = '/'.$prefix.'(\d+)/';

        if (! preg_match($pattern, $content, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    public function recordTransaction(array $payload): SePayTransaction
    {
        $transferType = (string) ($payload['transferType'] ?? '');
        $amount = (float) ($payload['transferAmount'] ?? 0);
        $sepayId = (string) ($payload['id'] ?? '');

        if ($sepayId === '') {
            $sepayId = 'local-'.uniqid();
        }

        return SePayTransaction::query()->updateOrCreate(
            ['sepay_id' => $sepayId],
            [
                'gateway' => $payload['gateway'] ?? null,
                'transaction_date' => $payload['transactionDate'] ?? null,
                'account_number' => $payload['accountNumber'] ?? null,
                'sub_account' => $payload['subAccount'] ?? null,
                'transfer_type' => $transferType,
                'amount_in' => $transferType === 'in' ? $amount : 0,
                'amount_out' => $transferType === 'out' ? $amount : 0,
                'accumulated' => $payload['accumulated'] ?? 0,
                'code' => $payload['code'] ?? null,
                'transaction_content' => $payload['content'] ?? null,
                'reference_number' => $payload['referenceCode'] ?? null,
                'description' => $payload['description'] ?? null,
                'payload' => $payload,
            ]
        );
    }
}
