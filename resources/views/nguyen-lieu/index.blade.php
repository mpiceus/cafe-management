@extends('layouts.app')

@section('title', 'Quản lý nguyên liệu')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý nguyên liệu</h1>
        <div class="text-muted">Theo dõi tồn kho, ngưỡng tối thiểu và nhà cung cấp</div>
    </div>
    @if($canManage)
        <a class="btn btn-primary" href="{{ route('nguyen-lieu.create') }}">Thêm nguyên liệu</a>
    @endif
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-6">
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tìm nguyên liệu">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sap_het">
                    <option value="">Tất cả</option>
                    <option value="1" @selected(($filters['sap_het'] ?? '') === '1')>Sắp hết</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
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
                    <th>Tên nguyên liệu</th>
                    <th>Đơn vị</th>
                    <th>Tồn kho</th>
                    <th>Tối thiểu</th>
                    <th>Nhà cung cấp</th>
                    <th>Tùy chỉnh</th>
                    @if($canManage)
                        <th class="text-end">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nguyenLieus as $nl)
                    <tr>
                        <td class="fw-semibold">{{ $nl->ten_nguyen_lieu }}</td>
                        <td>{{ $nl->don_vi_tinh }}</td>
                        <td><span class="{{ $nl->ton_kho <= $nl->so_luong_toi_thieu ? 'text-danger fw-semibold' : '' }}">{{ number_format($nl->ton_kho, 2, ',', '.') }}</span></td>
                        <td>{{ number_format($nl->so_luong_toi_thieu, 2, ',', '.') }}</td>
                        <td>{{ $nl->nhaCungCap?->ten_nha_cung_cap }}</td>
                        <td>{{ $nl->duoc_tuy_chinh ? 'Có' : 'Không' }}</td>
                        @if($canManage)
                            <td class="text-end">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('nguyen-lieu.edit', $nl) }}">Sửa</a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 7 : 6 }}" class="text-center text-muted py-4">Chưa có nguyên liệu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $nguyenLieus->links() }}</div>
</div>
@endsection
