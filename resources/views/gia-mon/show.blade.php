@extends('layouts.app')

@section('title', 'Lịch sử giá món')

@section('content')
@php($canManage = auth()->user()->chuc_vu === \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG)
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Lịch sử giá món</h1>
        <div class="text-muted">{{ $mon->ten_mon }} - {{ $mon->loaiMon?->ten_loai_mon }}</div>
    </div>
    <div class="d-flex gap-2">
        @if($canManage)
            <a class="btn btn-primary" href="{{ route('gia-mon.create', $mon) }}">Áp dụng giá mới</a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('mon.index') }}">Quay lại</a>
    </div>
</div>

<div class="card page-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Giá</th>
                    <th>Ngày áp dụng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lichSuGia as $giaMon)
                    <tr>
                        <td class="fw-semibold">{{ number_format($giaMon->gia, 0, ',', '.') }} đ</td>
                        <td>{{ $giaMon->ngay_ap_dung->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Chưa có lịch sử giá.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
