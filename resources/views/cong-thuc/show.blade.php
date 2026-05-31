@extends('layouts.app')

@section('title', 'Chi tiết công thức')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Chi tiết công thức</h1>
        <div class="text-muted">{{ $mon->ten_mon }} - {{ $mon->loaiMon?->ten_loai_mon }}</div>
    </div>
    <div class="d-flex gap-2">
        @if($canManage)
            <a class="btn btn-primary" href="{{ route('cong-thuc.edit', $mon) }}">Sửa công thức</a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('cong-thuc.index') }}">Quay lại</a>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nguyên liệu</th>
                    <th class="text-end">Khối lượng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($congThucs as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->nguyenLieu?->ten_nguyen_lieu }}</td>
                        <td class="text-end">{{ \App\Support\FormatHelper::number($row->so_luong) }} {{ $row->nguyenLieu?->don_vi_tinh }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Chưa có công thức.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
