@extends('layouts.app')

@section('title', 'Chi tiết đơn nhập')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Đơn nhập #{{ $donNhap->ma_don_nhap }}</h1>
        <div class="text-muted">{{ $donNhap->ngay_nhap->format('d/m/Y H:i') }} · {{ $donNhap->nhaCungCap?->ten_nha_cung_cap }}</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('don-nhap.index') }}">Quay lại</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card page-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Người tạo</div>
                <div class="fw-semibold">{{ $donNhap->nguoiDung?->ho_ten }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card page-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Số nguyên liệu</div>
                <div class="fw-semibold">{{ $donNhap->chiTiets->count() }} dòng</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card page-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Tổng tiền</div>
                <div class="fw-semibold fs-5">{{ number_format($donNhap->tong_tien, 0, ',', '.') }} đ</div>
            </div>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nguyên liệu</th>
                    <th>Số lượng mua</th>
                    <th>Số lượng nhập kho</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donNhap->chiTiets as $chiTiet)
                    @php
                        $heSo = in_array($chiTiet->don_vi_mua, ['kg', 'l'], true) ? 1000 : 1;
                        $soLuongNhapKho = $chiTiet->so_luong_nhap_kho ?? ($chiTiet->so_luong * $heSo * (float) ($chiTiet->nguyenLieu?->ti_le_su_dung ?? 1));
                        $thanhTien = (($chiTiet->so_luong * $heSo) / 100) * $chiTiet->don_gia;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $chiTiet->nguyenLieu?->ten_nguyen_lieu }}</div>
                            <div class="small text-muted">Đơn vị kho: {{ $chiTiet->nguyenLieu?->don_vi_tinh }}</div>
                        </td>
                        <td>{{ \App\Support\FormatHelper::number($chiTiet->so_luong) }} {{ $chiTiet->don_vi_mua ?? $chiTiet->nguyenLieu?->don_vi_tinh }}</td>
                        <td>{{ \App\Support\FormatHelper::number($soLuongNhapKho) }} {{ $chiTiet->nguyenLieu?->don_vi_tinh }}</td>
                        <td>{{ number_format($chiTiet->don_gia, 0, ',', '.') }} đ / 100{{ $chiTiet->nguyenLieu?->don_vi_tinh }}</td>
                        <td>{{ number_format($thanhTien, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
