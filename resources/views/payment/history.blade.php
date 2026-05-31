@extends('layouts.app')

@section('title', 'Lịch sử thanh toán')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Lịch sử thanh toán</h1>
        <div class="text-muted">Hóa đơn #{{ $hoaDon->ma_hoa_don }}</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('order.index') }}">Quay lại</a>
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><div class="text-muted small">Mã thanh toán</div><div class="fw-semibold">{{ $hoaDon->ma_thanh_toan }}</div></div>
            <div class="col-md-4"><div class="text-muted small">Tổng tiền</div><div class="fw-semibold">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</div></div>
            <div class="col-md-4"><div class="text-muted small">Trạng thái</div><div class="fw-semibold">{{ ['dang_tao' => 'Chờ thanh toán', 'da_thanh_toan' => 'Đã thanh toán', 'da_hoan_thanh' => 'Đã hoàn thành'][$hoaDon->trang_thai] ?? $hoaDon->trang_thai }}</div></div>
        </div>
    </div>
</div>

<div class="card page-card mb-3">
    <div class="card-header bg-white fw-semibold">Giao dịch SePay</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã giao dịch</th>
                    <th>Ngân hàng</th>
                    <th>Thời gian</th>
                    <th>Số tiền vào</th>
                    <th>Nội dung</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td>{{ $tx->sepay_id }}</td>
                        <td>{{ $tx->gateway }}</td>
                        <td>{{ optional($tx->transaction_date)->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($tx->amount_in, 0, ',', '.') }} đ</td>
                        <td class="small text-muted">{{ $tx->transaction_content }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có giao dịch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card page-card">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span>Yêu cầu hoàn tiền</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('payment.refund', $hoaDon) }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Số tiền hoàn</label>
                <input type="number" min="1" class="form-control" name="amount" required>
            </div>
            <div class="col-md-7">
                <label class="form-label">Lý do</label>
                <input class="form-control" name="reason">
            </div>
            <div class="col-md-2 d-grid">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-outline-danger">Gửi yêu cầu</button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                    <tr>
                        <td>#{{ $refund->id }}</td>
                        <td>{{ number_format($refund->amount, 0, ',', '.') }} đ</td>
                        <td>{{ $refund->status }}</td>
                        <td class="small text-muted">{{ $refund->reason }}</td>
                        <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có yêu cầu hoàn tiền.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
