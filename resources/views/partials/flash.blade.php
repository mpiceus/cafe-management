@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        Vui lòng kiểm tra lại thông tin đã nhập.
    </div>
@endif
