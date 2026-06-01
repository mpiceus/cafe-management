@extends('layouts.app')

@section('title', 'Pha chế')

@section('content')
<div class="mb-3">
    <h1 class="h4 mb-0">Pha chế</h1>
    <div class="text-muted">Theo dõi đơn chờ pha chế theo thứ tự ưu tiên sớm nhất.</div>
</div>

<div id="pha-che-message" class="alert d-none" role="alert"></div>
<div id="pha-che-grid" class="d-flex flex-column gap-3" data-fetch-url="{{ route('pha-che.data') }}" data-update-base-url="{{ url('pha-che') }}"></div>

@push('scripts')
    <script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'pha-che.js') }}"></script>
@endpush
@endsection
