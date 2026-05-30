@extends('layouts.app')

@section('title', 'Order và thanh toán')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Order và thanh toán</h1>
        <div class="text-muted">Danh sách hóa đơn từ mới nhất đến cũ nhất.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('order.create') }}">Tạo order mới</a>
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label">Mã hóa đơn</label>
                <input class="form-control" name="ma_hoa_don" value="{{ $filters['ma_hoa_don'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tên món</label>
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="tu_ngay" value="{{ $filters['tu_ngay'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="den_ngay" value="{{ $filters['den_ngay'] ?? '' }}">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-primary">Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã hóa đơn</th>
                    <th>Thời gian</th>
                    <th>Món</th>
                    <th>Thanh toán</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hoaDons as $hoaDon)
                    <tr>
                        <td>#{{ $hoaDon->ma_hoa_don }}</td>
                        <td>{{ $hoaDon->thoi_gian_tao->format('d/m/Y H:i') }}</td>
                        <td>
                            @foreach($hoaDon->chiTiets as $chiTiet)
                                <div class="small">{{ $chiTiet->mon?->ten_mon }} x {{ $chiTiet->so_luong }}</div>
                            @endforeach
                        </td>
                        <td>{{ $hoaDon->phuong_thuc_thanh_toan === 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt' }}</td>
                        <td>{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} đ</td>
                        <td>{{ ['dang_tao' => 'Đang tạo', 'da_thanh_toan' => 'Đã thanh toán', 'da_hoan_thanh' => 'Đã hoàn thành'][$hoaDon->trang_thai] ?? $hoaDon->trang_thai }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có hóa đơn.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $hoaDons->links() }}</div>
</div>
@endsection
