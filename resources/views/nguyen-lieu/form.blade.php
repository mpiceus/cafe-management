<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tên nguyên liệu</label>
        <input class="form-control @error('ten_nguyen_lieu') is-invalid @enderror" name="ten_nguyen_lieu" value="{{ old('ten_nguyen_lieu', $nguyenLieu->ten_nguyen_lieu) }}">
        @error('ten_nguyen_lieu')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Đơn vị tính</label>
        <select class="form-select @error('don_vi_tinh') is-invalid @enderror" name="don_vi_tinh">
            <option value="g" @selected(old('don_vi_tinh', $nguyenLieu->don_vi_tinh) === 'g')>g</option>
            <option value="ml" @selected(old('don_vi_tinh', $nguyenLieu->don_vi_tinh) === 'ml')>ml</option>
        </select>
        @error('don_vi_tinh')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Nhà cung cấp</label>
        <select class="form-select" name="ma_nha_cung_cap">
            @foreach($nhaCungCaps as $ncc)
                <option value="{{ $ncc->ma_nha_cung_cap }}" @selected(old('ma_nha_cung_cap', $nguyenLieu->ma_nha_cung_cap) == $ncc->ma_nha_cung_cap)>{{ $ncc->ten_nha_cung_cap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><label class="form-label">Tồn kho</label><input type="number" step="0.01" min="0" class="form-control" name="ton_kho" value="{{ old('ton_kho', $nguyenLieu->ton_kho ?? 0) }}"></div>
    <div class="col-md-3"><label class="form-label">Số lượng tối thiểu</label><input type="number" step="0.01" min="0" class="form-control" name="so_luong_toi_thieu" value="{{ old('so_luong_toi_thieu', $nguyenLieu->so_luong_toi_thieu ?? 0) }}"></div>
    <div class="col-md-3"><label class="form-label">Tỉ lệ sử dụng</label><input type="number" step="0.01" min="0.01" max="1" class="form-control" name="ti_le_su_dung" value="{{ old('ti_le_su_dung', $nguyenLieu->ti_le_su_dung ?? 1) }}"></div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="duoc_tuy_chinh" value="0">
            <input class="form-check-input" type="checkbox" name="duoc_tuy_chinh" value="1" @checked(old('duoc_tuy_chinh', $nguyenLieu->duoc_tuy_chinh))>
            <label class="form-check-label">Được tùy chỉnh</label>
        </div>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="duoc_su_dung" value="0">
            <input class="form-check-input" type="checkbox" name="duoc_su_dung" value="1" @checked(old('duoc_su_dung', $nguyenLieu->duoc_su_dung ?? true))>
            <label class="form-check-label">Được sử dụng</label>
        </div>
    </div>
</div>
<hr class="my-4">
