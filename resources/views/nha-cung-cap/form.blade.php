<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="ten_nha_cung_cap">Tên nhà cung cấp</label>
        <input id="ten_nha_cung_cap" name="ten_nha_cung_cap" class="form-control @error('ten_nha_cung_cap') is-invalid @enderror" value="{{ old('ten_nha_cung_cap', $nhaCungCap->ten_nha_cung_cap) }}">
        @error('ten_nha_cung_cap')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="so_dien_thoai">Số điện thoại</label>
        <input id="so_dien_thoai" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai', $nhaCungCap->so_dien_thoai) }}">
        @error('so_dien_thoai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $nhaCungCap->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-12">
        <label class="form-label" for="dia_chi">Địa chỉ</label>
        <textarea id="dia_chi" name="dia_chi" rows="3" class="form-control @error('dia_chi') is-invalid @enderror">{{ old('dia_chi', $nhaCungCap->dia_chi) }}</textarea>
        @error('dia_chi')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<hr class="my-4">
