@extends('layouts.app')

@section('title', 'AI Giám Sát')

@section('content')

<div class="card mb-4">
    <div class="card-body">

        <form method="POST" action="/ai-monitor/upload" enctype="multipart/form-data" class="d-flex gap-2">
            @csrf

            <input type="file" name="video" class="form-control">

            <button class="btn btn-primary">
                Upload Video
            </button>

        </form>

        <form method="POST" action="/ai-monitor/reset" class="mt-2">
            @csrf
            <button class="btn btn-danger">
                Reset mặc định
            </button>
        </form>
    </div>
</div>

<div class="row">

@foreach($videos as $video)

<div class="col-lg-12 mb-5">

<div class="card">

<div class="card-header">
    {{ $video->name }}
</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<h5>Video gốc</h5>

<video
    class="camera-video w-100"
    id="video-{{ $video->id }}"
    autoplay
    muted
    loop
    controls
>
    <source src="{{ asset('storage/'.$video->video_path) }}">
</video>

</div>

<div class="col-md-6">

<h5>AI Detect</h5>

<img id="detect-{{ $video->id }}" class="img-fluid border">

<div class="mt-3">

<div>
    Bàn có người:
    <span id="occupied-{{ $video->id }}">
        0
    </span>
</div>

<div>
    Bàn trống:
    <span id="unoccupied-{{ $video->id }}">
        0
    </span>
</div>

<div>
    Tổng:
    <span id="total-{{ $video->id }}">
        0
    </span>
</div>

</div>

</div>

</div>

</div>

</div>

</div>

@endforeach

</div>

@endsection

@push('scripts')
<script>
const API_URL = "http://192.168.1.7:8000/predict";

document.querySelectorAll('.camera-video').forEach(video => {

    const id = video.id.replace('video-', '');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    let isProcessing = false;

    async function sendFrame() {

        //nếu đang xử lý → KHÔNG tạo request mới
        if (isProcessing) return;

        //video chưa ready
        if (video.readyState < 2) {
            setTimeout(sendFrame, 1000);
            return;
        }

        isProcessing = true;

        // giảm resolution
        canvas.width = 640;
        canvas.height = 360;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(async (blob) => {

            const formData = new FormData();
            formData.append('file', blob, 'frame.jpg');

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                document.getElementById('occupied-' + id).innerText = data.occupied;
                document.getElementById('unoccupied-' + id).innerText = data.unoccupied;
                document.getElementById('total-' + id).innerText = data.total;

                document.getElementById('detect-' + id).src =
                    'data:image/jpeg;base64,' + data.image;

            } catch (e) {
                console.log("API error:", e);
            }

            isProcessing = false;

            setTimeout(sendFrame, 2500); // ~0.4 FPS

        }, 'image/jpeg', 0.6);
    }

    sendFrame();
});
</script>
@endpush