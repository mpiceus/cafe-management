@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('content')
@php
    $paymentLabel = $hoaDon->phuong_thuc_thanh_toan === 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt';
    $statusLabel = [
        'dang_tao' => 'Chờ thanh toán',
        'da_thanh_toan' => 'Đã thanh toán',
        'da_hoan_thanh' => 'Đã hoàn thành',
    ][$hoaDon->trang_thai] ?? $hoaDon->trang_thai;
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Chi tiết hóa đơn</h1>
        <div class="text-muted">Hóa đơn #{{ $hoaDon->ma_hoa_don }}</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('order.invoice', $hoaDon) }}">In PDF</a>
        <a class="btn btn-outline-secondary" href="{{ route('order.index') }}">Quay lại</a>
    </div>
</div>

<div class="card page-card mb-3">
    <div class="card-header bg-white fw-semibold">Thông tin hóa đơn</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Mã hóa đơn</div>
                <div class="fw-semibold">#{{ $hoaDon->ma_hoa_don }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Mã thanh toán</div>
                <div class="fw-semibold">{{ $hoaDon->ma_thanh_toan ?: '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Thời gian tạo</div>
                <div class="fw-semibold">{{ $hoaDon->thoi_gian_tao?->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Thời gian thanh toán</div>
                <div class="fw-semibold">{{ $hoaDon->thoi_gian_thanh_toan?->format('d/m/Y H:i') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Nhân viên tạo</div>
                <div class="fw-semibold">{{ $hoaDon->nguoiDung?->ho_ten ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Phương thức</div>
                <div class="fw-semibold">{{ $paymentLabel }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Trạng thái</div>
                <div class="fw-semibold">{{ $statusLabel }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Tổng tiền</div>
                <div class="fw-semibold">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</div>
            </div>
        </div>
    </div>
</div>

<div class="card page-card mb-3">
    <div class="card-header bg-white fw-semibold">Chi tiết món</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Món</th>
                    <th class="text-end">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th>Tùy chỉnh</th>
                    <th>Topping</th>
                    <th>Ghi chú</th>
                    <th class="text-end">Thành tiền món</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hoaDon->chiTiets as $chiTiet)
                    @php
                        $giaMon = (float) ($chiTiet->mon?->giaMoiNhat?->gia ?? 0);
                        $lineTotal = $giaMon * (int) $chiTiet->so_luong;
                        $cheDoLabel = [
                            'chi_nong' => 'Nóng',
                            'chi_lanh' => 'Lạnh',
                            'nong' => 'Nóng',
                            'lanh' => 'Lạnh',
                        ][$chiTiet->che_do] ?? $chiTiet->che_do;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $chiTiet->mon?->ten_mon }}</div>
                            @if($chiTiet->che_do)
                                <div class="small text-muted">Chế độ: {{ $cheDoLabel }}</div>
                            @endif
                        </td>
                        <td class="text-end">{{ $chiTiet->so_luong }}</td>
                        <td class="text-end">{{ number_format($giaMon, 0, ',', '.') }} đ</td>
                        <td>
                            @forelse($chiTiet->tuyChinhs as $tuyChinh)
                                <div class="small">{{ $tuyChinh->nguyenLieu?->ten_nguyen_lieu }}: {{ $tuyChinh->ti_le }}%</div>
                            @empty
                                <span class="text-muted small">Mặc định</span>
                            @endforelse
                        </td>
                        <td>
                            @forelse($chiTiet->toppings as $topping)
                                @php
                                    $toppingPrice = (float) ($topping->mon?->giaMoiNhat?->gia ?? 0);
                                @endphp
                                <div class="small">
                                    {{ $topping->mon?->ten_mon }} x {{ $topping->so_luong }}
                                    <span class="text-muted">({{ number_format($toppingPrice * (int) $topping->so_luong, 0, ',', '.') }} đ)</span>
                                </div>
                            @empty
                                <span class="text-muted small">Không có</span>
                            @endforelse
                        </td>
                        <td class="small text-muted">{{ $chiTiet->ghi_chu ?: '-' }}</td>
                        <td class="text-end">{{ number_format($lineTotal, 0, ',', '.') }} đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Hóa đơn chưa có món.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($hoaDon->phuong_thuc_thanh_toan === 'chuyen_khoan')
    <div class="card page-card">
        <div class="card-header bg-white fw-semibold">Giao dịch SePay</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã giao dịch</th>
                        <th>Ngân hàng</th>
                        <th>Thời gian</th>
                        <th class="text-end">Số tiền vào</th>
                        <th>Nội dung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->sepay_id }}</td>
                            <td>{{ $tx->gateway }}</td>
                            <td>{{ optional($tx->transaction_date)->format('d/m/Y H:i') }}</td>
                            <td class="text-end">{{ number_format($tx->amount_in, 0, ',', '.') }} đ</td>
                            <td class="small text-muted">{{ $tx->transaction_content }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có giao dịch.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
