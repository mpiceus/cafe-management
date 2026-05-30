@extends('layouts.app')

@section('title', 'Công thức pha chế')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Công thức pha chế</h1>
        <div class="text-muted">Tìm theo tên món hoặc nguyên liệu và chỉnh sửa theo từng widget</div>
    </div>
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET">
            <div class="col-md-7">
                <label class="form-label">Tìm kiếm món hoặc nguyên liệu</label>
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Ví dụ: trà sữa, đường, dừa khô">
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
            <div class="col-md-2 d-grid"><button class="btn btn-outline-primary">Lọc</button></div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($mons as $mon)
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('cong-thuc.edit', $mon) }}" class="card widget-card h-100 text-decoration-none text-body">
                <div class="card-body">
                    <div class="fw-bold fs-5 mb-1">{{ $mon->ten_mon }}</div>
                    <div class="text-muted small mb-3">{{ $mon->loaiMon?->ten_loai_mon }}</div>
                    <div class="small d-flex flex-column gap-2">
                        @forelse($mon->congThucs as $item)
                            <div class="d-flex justify-content-between gap-3">
                                <span>{{ $item->nguyenLieu?->ten_nguyen_lieu }}</span>
                                <span class="text-muted">{{ number_format($item->so_luong, 2, ',', '.') }} {{ $item->nguyenLieu?->don_vi_tinh }}</span>
                            </div>
                        @empty
                            <span class="text-muted">Chưa có công thức.</span>
                        @endforelse
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12"><div class="card page-card"><div class="card-body text-muted text-center">Không có món phù hợp.</div></div></div>
    @endforelse
</div>
@endsection
