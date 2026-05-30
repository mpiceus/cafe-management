@extends('layouts.app')

@section('title', 'Thêm món')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thêm món</h1>
    <a class="btn btn-outline-secondary" href="{{ route('mon.index') }}">Quay lại</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ route('mon.store') }}" enctype="multipart/form-data" data-persist-key="mon-create" data-persist-clear-on-submit="1">
            @csrf
            @include('mon.form', ['mon' => $mon])
            <button class="btn btn-primary" type="submit">Lưu</button>
        </form>
    </div>
</div>
@endsection
