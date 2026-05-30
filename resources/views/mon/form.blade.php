<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="ten_mon">Tên món</label>
        <input id="ten_mon" name="ten_mon" class="form-control @error('ten_mon') is-invalid @enderror" value="{{ old('ten_mon', $mon->ten_mon) }}">
        @error('ten_mon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="ma_loai_mon">Loại món</label>
        <select id="ma_loai_mon" name="ma_loai_mon" class="form-select @error('ma_loai_mon') is-invalid @enderror">
            <option value="">Chọn loại món</option>
            @foreach($loaiMons as $loaiMon)
                <option value="{{ $loaiMon->ma_loai_mon }}" @selected(old('ma_loai_mon', $mon->ma_loai_mon) == $loaiMon->ma_loai_mon)>{{ $loaiMon->ten_loai_mon }}</option>
            @endforeach
        </select>
        @error('ma_loai_mon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @unless($mon->exists)
        <div class="col-md-4">
            <label class="form-label" for="gia">Giá món ban đầu</label>
            <input id="gia" name="gia" type="number" min="0" step="100" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia') }}">
            @error('gia')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label" for="ngay_ap_dung">Ngày áp dụng giá</label>
            <input id="ngay_ap_dung" name="ngay_ap_dung" type="datetime-local" class="form-control @error('ngay_ap_dung') is-invalid @enderror" value="{{ old('ngay_ap_dung', now()->format('Y-m-d\TH:i')) }}">
            @error('ngay_ap_dung')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @else
        <div class="col-md-4">
            <label class="form-label">Giá hiện tại</label>
            <div class="form-control bg-light">{{ $mon->giaMoiNhat ? number_format($mon->giaMoiNhat->gia, 0, ',', '.') . ' đ' : 'Chưa có giá' }}</div>
        </div>
    @endunless

    <div class="col-md-4">
        <label class="form-label" for="che_do_phuc_vu">Chế độ phục vụ</label>
        <select id="che_do_phuc_vu" name="che_do_phuc_vu" class="form-select @error('che_do_phuc_vu') is-invalid @enderror">
            @foreach([\App\Models\Mon::CHE_DO_CA_HAI => 'Cả hai', \App\Models\Mon::CHE_DO_CHI_NONG => 'Chỉ nóng', \App\Models\Mon::CHE_DO_CHI_LANH => 'Chỉ lạnh', \App\Models\Mon::CHE_DO_KHONG_AP_DUNG => 'Không áp dụng'] as $value => $label)
                <option value="{{ $value }}" @selected(old('che_do_phuc_vu', $mon->che_do_phuc_vu ?: \App\Models\Mon::CHE_DO_KHONG_AP_DUNG) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror">
            <option value="{{ \App\Models\Mon::TRANG_THAI_DANG_BAN }}" @selected(old('trang_thai', $mon->trang_thai ?: \App\Models\Mon::TRANG_THAI_DANG_BAN) === \App\Models\Mon::TRANG_THAI_DANG_BAN)>Đang bán</option>
            <option value="{{ \App\Models\Mon::TRANG_THAI_DUNG_BAN }}" @selected(old('trang_thai', $mon->trang_thai) === \App\Models\Mon::TRANG_THAI_DUNG_BAN)>Dừng bán</option>
        </select>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="cho_them_topping" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="cho_them_topping" name="cho_them_topping" value="1" @checked(old('cho_them_topping', $mon->cho_them_topping))>
            <label class="form-check-label" for="cho_them_topping">Cho thêm topping</label>
        </div>
    </div>

    <div class="col-md-8">
        <label class="form-label" for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta" rows="4" class="form-control @error('mo_ta') is-invalid @enderror">{{ old('mo_ta', $mon->mo_ta) }}</textarea>
        @error('mo_ta')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="hinh_anh_file">Hình ảnh</label>
        <input id="hinh_anh_file" name="hinh_anh_file" type="file" accept="image/*" class="form-control @error('hinh_anh_file') is-invalid @enderror">
        @error('hinh_anh_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($mon->hinh_anh)
            <div class="mt-3 border rounded overflow-hidden" style="width: 160px; height: 110px;">
                <img src="{{ asset('storage/'.$mon->hinh_anh) }}" alt="{{ $mon->ten_mon }}" class="w-100 h-100 object-fit-cover">
            </div>
        @endif
    </div>
</div>

<hr class="my-4">
