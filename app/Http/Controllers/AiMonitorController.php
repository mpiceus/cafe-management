<?php

namespace App\Http\Controllers;

use App\Models\CameraVideo;
use Illuminate\Http\Request;

class AiMonitorController extends Controller
{
    public function index()
    {
        $videos = CameraVideo::all();

        return view(
            'ai-monitor.index',
            compact('videos')
        );
    }

    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi'
        ]);

        $path = $request
            ->file('video')
            ->store('videos', 'public');

        CameraVideo::create([
            'name' => 'Video Demo',
            'video_path' => $path,
            'is_default' => false
        ]);

        return back()
            ->with('success', 'Upload thành công');
    }

    public function reset()
    {
        CameraVideo::where(
            'is_default',
            false
        )->delete();

        return back()
            ->with('success', 'Đã reset');
    }
}