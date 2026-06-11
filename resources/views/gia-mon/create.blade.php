@extends('layouts.app')

@section('title', 'Áp dụng giá mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Áp dụng giá mới</h1>
        <div class="text-muted">{{ $mon->ten_mon }} - {{ $mon->loaiMon?->ten_loai_mon }}</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('gia-mon.index') }}">Quay lại</a>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body">
                <form method="POST" action="{{ route('gia-mon.store', $mon) }}" data-persist-key="gia-mon-{{ $mon->ma_mon }}" data-persist-clear-on-submit="1">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="size">Size</label>
                        <select id="size" name="size" class="form-select @error('size') is-invalid @enderror">
                            @foreach(['S', 'M', 'L'] as $size)
                                <option value="{{ $size }}" @selected(old('size', 'S') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                        @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="gia">Giá bán</label>
                        <input id="gia" name="gia" type="number" min="0" step="100" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia', $mon->giaMoiNhat?->gia) }}">
                        @error('gia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="ngay_ap_dung">Ngày áp dụng</label>
                        <input id="ngay_ap_dung" name="ngay_ap_dung" type="datetime-local" class="form-control @error('ngay_ap_dung') is-invalid @enderror" value="{{ old('ngay_ap_dung', now()->format('Y-m-d\TH:i')) }}">
                        @error('ngay_ap_dung')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button class="btn btn-primary" type="submit">Lưu giá mới</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-header bg-white fw-semibold">Lịch sử giá</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Giá</th><th>Ngày áp dụng</th></tr>
                    </thead>
                    <tbody>
                        @forelse($lichSuGia as $giaMon)
                            <tr><td>{{ number_format($giaMon->gia, 0, ',', '.') }} đ</td><td>{{ $giaMon->ngay_ap_dung->format('d/m/Y H:i') }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">Chưa có lịch sử giá.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
