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
                    <th>Trạng thái</th>
                    @if($canManage)
                        <th class="text-end">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nguyenLieus as $nl)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $nl->ten_nguyen_lieu }}</div>
                        </td>
                        <td>{{ $nl->don_vi_tinh }}</td>
                        <td><span class="{{ $nl->ton_kho <= $nl->so_luong_toi_thieu ? 'text-danger fw-semibold' : '' }}">{{ number_format($nl->ton_kho, 2, ',', '.') }}</span></td>
                        <td>{{ number_format($nl->so_luong_toi_thieu, 2, ',', '.') }}</td>
                        <td>{{ $nl->nhaCungCap?->ten_nha_cung_cap }}</td>
                        <td>{{ $nl->duoc_tuy_chinh ? 'Có' : 'Không' }}</td>
                        <td>
                            <span class="badge {{ $nl->duoc_su_dung ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $nl->duoc_su_dung ? 'Đang dùng' : 'Ngừng dùng' }}
                            </span>
                        </td>
                        @if($canManage)
                            <td class="text-end">
                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('nguyen-lieu.edit', $nl) }}">Sửa</a>
                                    <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteNguyenLieu{{ $nl->ma_nguyen_lieu }}">Xóa</button>
                                    @if(! $nl->co_the_xoa)
                                        @if($nl->duoc_su_dung)
                                            <form method="POST" action="{{ route('nguyen-lieu.stop-using', $nl) }}" onsubmit="return confirm('Ngừng sử dụng nguyên liệu này?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-outline-secondary btn-sm" type="submit">Ngừng dùng</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 8 : 7 }}" class="text-center text-muted py-4">Chưa có nguyên liệu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $nguyenLieus->links() }}</div>
</div>

@if($canManage)
    @foreach($nguyenLieus as $nl)
        <div class="modal fade" id="deleteNguyenLieu{{ $nl->ma_nguyen_lieu }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa nguyên liệu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        @if($nl->co_the_xoa)
                            Xóa <strong>{{ $nl->ten_nguyen_lieu }}</strong>. Hành động này chỉ thực hiện được khi nguyên liệu chưa xuất hiện trong công thức, đơn nhập hoặc tùy chỉnh hóa đơn.
                        @else
                            {{ $nl->ly_do_khong_xoa }}
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        @if($nl->co_the_xoa)
                            <form method="POST" action="{{ route('nguyen-lieu.destroy', $nl) }}">
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
