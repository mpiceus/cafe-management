<?php

return [
    'merchant_id' => env('SEPAY_MERCHANT_ID', ''),
    'secret_key' => env('SEPAY_SECRET_KEY', ''),
    'webhook_key' => env('SEPAY_WEBHOOK_KEY', ''),
    'mode' => env('SEPAY_MODE', 'sandbox'),

    'bank_account' => env('SEPAY_BANK_ACCOUNT', ''),
    'bank_code' => env('SEPAY_BANK_CODE', ''),
    'bank_name' => env('SEPAY_BANK_NAME', ''),
    'account_name' => env('SEPAY_ACCOUNT_NAME', ''),

    'qr_template' => env('SEPAY_QR_TEMPLATE', 'compact'),
    'payment_prefix' => env('SEPAY_PAYMENT_PREFIX', 'DH'),

    'checkout_url' => env('SEPAY_CHECKOUT_URL', 'https://pay.sepay.vn/v1/checkout/init'),
    'refund_url' => env('SEPAY_REFUND_URL', ''),
    'status_url' => env('SEPAY_STATUS_URL', ''),
];
