@extends('layouts.app')

@section('title', 'Nhập hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Nhập hàng</h1>
        <div class="text-muted">Tìm theo nhà cung cấp, nguyên liệu hoặc người tạo đơn</div>
    </div>
    <a class="btn btn-primary" href="{{ route('don-nhap.create') }}">Tạo đơn nhập</a>
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-10">
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tìm theo nhà cung cấp, nguyên liệu, người tạo">
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
                    <th>Mã</th>
                    <th>Ngày nhập</th>
                    <th>Nhà cung cấp</th>
                    <th>Người tạo</th>
                    <th>Tổng tiền</th>
                    <th>Chi tiết nhanh</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($donNhaps as $dn)
                    <tr>
                        <td>#{{ $dn->ma_don_nhap }}</td>
                        <td>{{ $dn->ngay_nhap->format('d/m/Y H:i') }}</td>
                        <td>{{ $dn->nhaCungCap?->ten_nha_cung_cap }}</td>
                        <td>{{ $dn->nguoiDung?->ho_ten }}</td>
                        <td>{{ number_format($dn->tong_tien, 0, ',', '.') }} đ</td>
                        <td>
                            @foreach($dn->chiTiets as $ct)
                                <div class="small">
                                    {{ $ct->nguyenLieu?->ten_nguyen_lieu }}:
                                    {{ number_format($ct->so_luong, 2, ',', '.') }} {{ $ct->don_vi_mua ?? $ct->nguyenLieu?->don_vi_tinh }}
                                    x {{ number_format($ct->don_gia, 0, ',', '.') }} đ
                                </div>
                            @endforeach
                        </td>
                        <td class="text-end">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('don-nhap.show', $dn) }}">Xem đơn</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có đơn nhập.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $donNhaps->links() }}</div>
</div>
@endsection
