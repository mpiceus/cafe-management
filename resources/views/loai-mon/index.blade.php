@extends('layouts.app')

@section('title', 'Quản lý loại món')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý loại món</h1>
        <div class="text-muted">Bấm vào tên loại món hoặc số món để xem danh sách món đã lọc</div>
    </div>
    @if($canManage)
        <a class="btn btn-primary" href="{{ route('loai-mon.create') }}">Thêm loại món</a>
    @endif
</div>

<div class="card page-card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('loai-mon.index') }}">
            <div class="col-md-10">
                <input class="form-control" name="tu_khoa" value="{{ $filters['tu_khoa'] ?? '' }}" placeholder="Tìm theo tên hoặc mô tả">
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
                    <th>Tên loại món</th>
                    <th>Mô tả</th>
                    <th>Số món</th>
                    @if($canManage)
                        <th class="text-end">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($loaiMons as $loaiMon)
                    <tr>
                        <td>
                            <a href="{{ route('mon.index', ['ma_loai_mon' => $loaiMon->ma_loai_mon]) }}" class="fw-semibold text-decoration-none">
                                {{ $loaiMon->ten_loai_mon }}
                            </a>
                        </td>
                        <td>{{ $loaiMon->mo_ta ?: 'Không có mô tả' }}</td>
                        <td>
                            <a href="{{ route('mon.index', ['ma_loai_mon' => $loaiMon->ma_loai_mon]) }}" class="badge text-bg-light text-decoration-none">
                                {{ $loaiMon->mons_count }}
                            </a>
                        </td>
                        @if($canManage)
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('loai-mon.edit', $loaiMon) }}">Sửa</a>
                                    <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteLoaiMon{{ $loaiMon->ma_loai_mon }}">Xóa</button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 4 : 3 }}" class="text-center text-muted py-4">Chưa có loại món.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $loaiMons->links() }}</div>
</div>

@if($canManage)
    @foreach($loaiMons as $loaiMon)
        <div class="modal fade" id="deleteLoaiMon{{ $loaiMon->ma_loai_mon }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa loại món</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        @if($loaiMon->co_the_xoa)
                            Xóa <strong>{{ $loaiMon->ten_loai_mon }}</strong>. Chỉ loại món chưa có món nào thuộc loại này mới được xóa.
                        @else
                            {{ $loaiMon->ly_do_khong_xoa }}
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        @if($loaiMon->co_the_xoa)
                            <form method="POST" action="{{ route('loai-mon.destroy', $loaiMon) }}">
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
