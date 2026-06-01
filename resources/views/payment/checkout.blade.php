@extends('layouts.app')

@section('title', 'Thanh toán chuyển khoản')

@section('content')
<div class="mb-3">
    <h1 class="h4 mb-0">Thanh toán chuyển khoản</h1>
    <div class="text-muted">Quét mã QR hoặc chuyển khoản theo thông tin bên dưới.</div>
</div>

<div id="payment-message" class="alert d-none" role="alert"></div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card page-card h-100">
            <div class="card-body">
                <div class="fw-semibold mb-2">Thông tin hóa đơn</div>
                <div class="d-flex justify-content-between"><span>Mã hóa đơn</span><strong>#{{ $hoaDon->ma_hoa_don }}</strong></div>
                <div class="d-flex justify-content-between"><span>Mã thanh toán</span><strong>{{ $paymentCode }}</strong></div>
                <div class="d-flex justify-content-between"><span>Tổng tiền</span><strong>{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</strong></div>
                <div class="d-flex justify-content-between"><span>Trạng thái</span><strong id="payment-status" data-status-url="{{ route('payment.status', $hoaDon, false) }}">{{ $hoaDon->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán' }}</strong></div>
                <div class="text-muted small mt-2">Ghi nội dung chuyển khoản chính xác để hệ thống nhận diện.</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card page-card h-100">
            <div class="card-body">
                <div class="fw-semibold mb-2">Quét mã QR</div>
                <div class="d-flex flex-column align-items-center">
                    <img class="img-fluid border rounded" src="{{ $qrUrl }}" alt="QR thanh toán">
                </div>
                <div class="mt-3 small text-muted">
                    <div><strong>Ngân hàng:</strong> {{ $bankName ?: $bankCode }}</div>
                    <div><strong>Số tài khoản:</strong> {{ $bankAccount }}</div>
                    <div><strong>Chủ tài khoản:</strong> {{ $accountName }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('order.index') }}">Quay lại danh sách</a>
    <a id="invoice-link" class="btn btn-primary d-none" href="{{ route('order.invoice', $hoaDon) }}">In hóa đơn</a>
</div>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'payment-checkout.js') }}"></script>
@endpush
@endsection
