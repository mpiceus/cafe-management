@extends('layouts.app')

@section('title', 'Người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Người dùng</h1>
        <div class="text-muted">Quản lý tài khoản và phân quyền</div>
    </div>
    <a class="btn btn-primary" href="{{ route('nguoi-dung.create') }}">Thêm người dùng</a>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Họ tên</th>
                    <th>Tên đăng nhập</th>
                    <th>Chức vụ</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nguoiDungs as $nguoiDung)
                    <tr>
                        <td>{{ $nguoiDung->ho_ten }}</td>
                        <td>{{ $nguoiDung->ten_dang_nhap }}</td>
                        <td>{{ str_replace('_', ' ', $nguoiDung->chuc_vu) }}</td>
                        <td>
                            <span class="badge {{ $nguoiDung->dangHoatDong() ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $nguoiDung->dangHoatDong() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('nguoi-dung.edit', $nguoiDung) }}">Sửa</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có người dùng.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $nguoiDungs->links() }}
    </div>
</div>
@endsection
