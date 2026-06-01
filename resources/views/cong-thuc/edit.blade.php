@extends('layouts.app')

@section('title', 'Cập nhật công thức')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div>
        <h1 class="h4 mb-0">Cập nhật công thức</h1>
        <div class="text-muted">{{ $mon->ten_mon }} - {{ $mon->loaiMon?->ten_loai_mon }}</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('cong-thuc.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('cong-thuc.update', $mon) }}" data-persist-key="cong-thuc-{{ $mon->ma_mon }}" data-persist-clear-on-submit="1">
            @csrf
            @method('PUT')
            <div id="formula-list" class="d-flex flex-column gap-3 mb-3">
                @foreach($congThucs as $index => $row)
                    <div class="row g-3 align-items-end formula-row">
                        <div class="col-md-7">
                            <label class="form-label">Nguyên liệu</label>
                            <select class="form-select" name="items[{{ $index }}][ma_nguyen_lieu]">
                                <option value="">Chọn nguyên liệu</option>
                                @foreach($nguyenLieus as $nl)
                                    <option value="{{ $nl->ma_nguyen_lieu }}" @selected($row->ma_nguyen_lieu == $nl->ma_nguyen_lieu)>{{ $nl->ten_nguyen_lieu }} ({{ $nl->don_vi_tinh }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Khối lượng hiện tại</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="items[{{ $index }}][so_luong]" value="{{ $row->so_luong }}">
                        </div>
                        <div class="col-md-1"><button class="btn btn-outline-danger w-100 remove-row" type="button">-</button></div>
                    </div>
                @endforeach
            </div>

            <div id="formula-add" class="draft-add mb-4">+ Thêm nguyên liệu</div>
            <button class="btn btn-primary">Lưu công thức</button>
        </form>
    </div>
</div>
<script type="application/json" id="formula-options">@json($nguyenLieus->map(fn ($item) => ['id' => $item->ma_nguyen_lieu, 'label' => $item->ten_nguyen_lieu . ' (' . $item->don_vi_tinh . ')'])->values())</script>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'cong-thuc-edit.js') }}"></script>
@endpush
@endsection
