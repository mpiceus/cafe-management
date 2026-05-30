<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="ho_ten">Họ tên</label>
        <input id="ho_ten" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" value="{{ old('ho_ten', $nguoiDung->ho_ten) }}">
        @error('ho_ten')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="ten_dang_nhap">Tên đăng nhập</label>
        <input id="ten_dang_nhap" name="ten_dang_nhap" class="form-control @error('ten_dang_nhap') is-invalid @enderror" value="{{ old('ten_dang_nhap', $nguoiDung->ten_dang_nhap) }}">
        @error('ten_dang_nhap')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="mat_khau">Mật khẩu</label>
        <input id="mat_khau" name="mat_khau" type="password" class="form-control @error('mat_khau') is-invalid @enderror">
        @error('mat_khau')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($nguoiDung->exists)
            <div class="form-text">Để trống nếu không đổi mật khẩu.</div>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label" for="chuc_vu">Chức vụ</label>
        <select id="chuc_vu" name="chuc_vu" class="form-select @error('chuc_vu') is-invalid @enderror">
            @foreach([
                \App\Models\NguoiDung::CHUC_VU_CHU_CUA_HANG => 'Chủ cửa hàng',
                \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_ORDER => 'Nhân viên order',
                \App\Models\NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE => 'Nhân viên pha chế',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('chuc_vu', $nguoiDung->chuc_vu) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('chuc_vu')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror">
            @foreach([
                \App\Models\NguoiDung::TRANG_THAI_HOAT_DONG => 'Hoạt động',
                \App\Models\NguoiDung::TRANG_THAI_NGUNG_HOAT_DONG => 'Ngừng hoạt động',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('trang_thai', $nguoiDung->trang_thai ?: \App\Models\NguoiDung::TRANG_THAI_HOAT_DONG) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('trang_thai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr class="my-4">
