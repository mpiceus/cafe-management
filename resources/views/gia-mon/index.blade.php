@extends('layouts.app')

@section('title', 'Quản lý giá bán')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý giá bán</h1>
        <div class="text-muted">Tra cứu và cập nhật giá bán hiện hành của từng món</div>
    </div>
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm món</label>
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tên món">
            </div>
            <div class="col-md-3">
                <label class="form-label">Loại món</label>
                <select class="form-select" name="ma_loai_mon">
                    <option value="">Tất cả</option>
                    @foreach($loaiMons as $loaiMon)
                        <option value="{{ $loaiMon->ma_loai_mon }}" @selected(($filters['ma_loai_mon'] ?? '') == $loaiMon->ma_loai_mon)>{{ $loaiMon->ten_loai_mon }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tình trạng giá</label>
                <select class="form-select" name="trang_thai_gia">
                    <option value="">Tất cả</option>
                    <option value="co_gia" @selected(($filters['trang_thai_gia'] ?? '') === 'co_gia')>Đã có giá</option>
                    <option value="chua_co_gia" @selected(($filters['trang_thai_gia'] ?? '') === 'chua_co_gia')>Chưa có giá</option>
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-outline-primary">Lọc</button></div>
        </form>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Món</th>
                    <th>Loại</th>
                    <th>Giá hiện tại</th>
                    <th>Ngày áp dụng</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mons as $mon)
                    <tr>
                        <td class="fw-semibold">{{ $mon->ten_mon }}</td>
                        <td>{{ $mon->loaiMon?->ten_loai_mon }}</td>
                        <td>{{ $mon->giaMoiNhat ? number_format($mon->giaMoiNhat->gia, 0, ',', '.') . ' đ' : 'Chưa có giá' }}</td>
                        <td>{{ $mon->giaMoiNhat?->ngay_ap_dung?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('gia-mon.create', $mon) }}">Áp dụng giá mới</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Không có món phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $mons->links() }}</div>
</div>
@endsection
