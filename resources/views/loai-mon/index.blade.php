@extends('layouts.app')

@section('title', 'Quản lý loại món')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Quản lý loại món</h1>
        <div class="text-muted">Thêm, sửa và tìm kiếm nhóm món trong menu</div>
    </div>
    <a class="btn btn-primary" href="{{ route('loai-mon.create') }}">Thêm loại món</a>
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
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loaiMons as $loaiMon)
                    <tr>
                        <td class="fw-semibold">{{ $loaiMon->ten_loai_mon }}</td>
                        <td>{{ $loaiMon->mo_ta ?: 'Không có mô tả' }}</td>
                        <td>{{ $loaiMon->mons_count }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('loai-mon.edit', $loaiMon) }}">Sửa</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Chưa có loại món.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $loaiMons->links() }}</div>
</div>
@endsection
