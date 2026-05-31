<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="ten_loai_mon">Tên loại món</label>
        <input id="ten_loai_mon" name="ten_loai_mon" class="form-control @error('ten_loai_mon') is-invalid @enderror" value="{{ old('ten_loai_mon', $loaiMon->ten_loai_mon) }}">
        @error('ten_loai_mon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-12">
        <label class="form-label" for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta" rows="4" class="form-control @error('mo_ta') is-invalid @enderror">{{ old('mo_ta', $loaiMon->mo_ta) }}</textarea>
        @error('mo_ta')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<hr class="my-4">
