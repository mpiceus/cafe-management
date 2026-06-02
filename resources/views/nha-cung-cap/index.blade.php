@extends('layouts.app')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý nhà cung cấp</h1>
        <div class="text-muted">Quản lý thông tin liên hệ và địa chỉ nhà cung cấp</div>
    </div>
    @if($canManage)
        <a class="btn btn-primary" href="{{ route('nha-cung-cap.create') }}">Thêm nhà cung cấp</a>
    @endif
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('nha-cung-cap.index') }}">
            <div class="col-md-10">
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tìm tên, số điện thoại, email hoặc địa chỉ">
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
                    <th>Nhà cung cấp</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th>Liên quan</th>
                    @if($canManage)
                        <th class="text-end">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nhaCungCaps as $nhaCungCap)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $nhaCungCap->ten_nha_cung_cap }}</div>
                        </td>
                        <td>{{ $nhaCungCap->so_dien_thoai ?: '-' }}</td>
                        <td>{{ $nhaCungCap->email ?: '-' }}</td>
                        <td>{{ $nhaCungCap->dia_chi ?: '-' }}</td>
                        <td>
                            <div class="small">Nguyên liệu: {{ $nhaCungCap->nguyen_lieus_count }}</div>
                            <div class="small">Đơn nhập: {{ $nhaCungCap->don_nhaps_count }}</div>
                        </td>
                        @if($canManage)
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('nha-cung-cap.edit', $nhaCungCap) }}">Sửa</a>
                                    <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteNhaCungCap{{ $nhaCungCap->ma_nha_cung_cap }}">Xóa</button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 6 : 5 }}" class="text-center text-muted py-4">Chưa có nhà cung cấp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $nhaCungCaps->links() }}</div>
</div>

@if($canManage)
    @foreach($nhaCungCaps as $nhaCungCap)
        <div class="modal fade" id="deleteNhaCungCap{{ $nhaCungCap->ma_nha_cung_cap }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa nhà cung cấp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        @if($nhaCungCap->co_the_xoa)
                            Xóa <strong>{{ $nhaCungCap->ten_nha_cung_cap }}</strong>. Hành động này chỉ thực hiện được khi nhà cung cấp chưa có nguyên liệu hoặc đơn nhập liên quan.
                        @else
                            {{ $nhaCungCap->ly_do_khong_xoa }}
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        @if($nhaCungCap->co_the_xoa)
                            <form method="POST" action="{{ route('nha-cung-cap.destroy', $nhaCungCap) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Xóa</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
