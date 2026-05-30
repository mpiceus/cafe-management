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
@endsection
