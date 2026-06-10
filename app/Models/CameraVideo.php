<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CameraVideo extends Model
{
    protected $fillable = [
        'name',
        'video_path',
        'is_default'
    ];
}