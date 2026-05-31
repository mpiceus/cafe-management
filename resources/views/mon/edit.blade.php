@extends('layouts.app')

@section('title', 'Cập nhật món')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Cập nhật món</h1>
    <a class="btn btn-outline-secondary" href="{{ route('mon.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('mon.update', $mon) }}" enctype="multipart/form-data" data-persist-key="mon-edit-{{ $mon->ma_mon }}" data-persist-clear-on-submit="1">
            @csrf
            @method('PUT')
            @include('mon.form', ['mon' => $mon])
            <button class="btn btn-primary" type="submit">Cập nhật</button>
        </form>
    </div>
</div>
<!-- 
Form dùng để cập nhật thông tin món ăn đã tồn tại.

action="{{ route('mon.update', $mon) }}" => Gửi dữ liệu đến route mon.update của món hiện tại ($mon).

method="POST" => HTML chỉ hỗ trợ GET và POST nên form phải dùng POST.

@method('PUT')
=> Laravel tạo hidden input _method=PUT.
=> Khi submit, Laravel hiểu đây là request PUT.
=> PUT trong RESTful được dùng cho chức năng UPDATE (cập nhật dữ liệu).

@csrf => Sinh CSRF Token để chống giả mạo request từ website khác.

@include('mon.form', ['mon' => $mon])
=> Nhúng file mon/form.blade.php chứa các input như tên món, giá, hình ảnh...
=> Tách riêng để dùng lại cho cả form Thêm mới (Create) và Chỉnh sửa (Edit).

HTTP Method	  Chức năng
GET	          Lấy dữ liệu
POST	      Tạo mới dữ liệu
PUT	          Cập nhật dữ liệu đã có
DELETE	      Xóa dữ liệu

Ví dụ:
POST   /mon      => Thêm món mới
PUT    /mon/5    => Cập nhật món có mã 5
DELETE /mon/5    => Xóa món có mã 5
GET    /mon/5    => Xem món có mã 5
 -->
@endsection
