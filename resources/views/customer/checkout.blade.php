<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Thanh toán - Hóa đơn #{{ $hoaDon->ma_hoa_don }}</title>
    <link rel="stylesheet" href="{{ \App\Http\Controllers\ResourceAssetController::url('css', 'customer-checkout.css') }}">
</head>
<body>
    <div class="container">
        <div id="customer-checkout" class="card" data-status-url="{{ route('api.payment.status', $hoaDon, false) }}">
            <div id="statusArea">
                <div class="checkout-prompt">Quét QR để thanh toán</div>
                <div class="amount">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</div>
                <div class="qr">
                    <img class="qr-img" id="qrImage" src="{{ $qrUrl }}" alt="QR thanh toán">
                </div>
                <div class="bank">
                    <div><strong>Ngân hàng:</strong> {{ $bankName }} ({{ $bankCode }})</div>
                    <div><strong>Số tài khoản:</strong> {{ $bankAccount }}</div>
                    <div><strong>Chủ tài khoản:</strong> {{ $accountName }}</div>
                    <div class="order-reference">Mã đơn: #{{ $hoaDon->ma_hoa_don }}</div>
                </div>
            </div>

            <div id="successArea" class="success hidden">
                <div class="success-title">Thanh toán thành công!</div>
                <div class="success-reference">Mã đơn của bạn là <strong>#{{ $hoaDon->ma_hoa_don }}</strong>.</div>
                <div class="success-message">Vui lòng đợi, đồ uống của bạn đang được pha chế.</div>
            </div>
        </div>
    </div>

<script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'customer-checkout.js') }}"></script>
</body>
</html>
