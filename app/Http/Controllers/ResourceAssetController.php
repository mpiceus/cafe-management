<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class ResourceAssetController extends Controller
{
    private const ALLOWED_FILES = [
        'css' => [
            'bao-cao.css',
            'customer-checkout.css',
            'layout.css',
            'order-create.css',
            'welcome.css',
        ],
        'js' => [
            'bao-cao.js',
            'cong-thuc-edit.js',
            'customer-checkout.js',
            'don-nhap-create.js',
            'layout.js',
            'order-create.js',
            'payment-checkout.js',
            'pha-che.js',
        ],
    ];

    public static function url(string $type, string $file): string
    {
        $path = resource_path($type.'/'.$file);

        return URL::route('resource.asset', [
            'type' => $type,
            'file' => $file,
            'v' => File::lastModified($path),
        ]);
    }

    public function __invoke(string $type, string $file): Response
    {
        abort_unless(in_array($file, self::ALLOWED_FILES[$type] ?? [], true), 404);

        $path = resource_path($type.'/'.$file);
        abort_unless(File::isFile($path), 404);

        return response(File::get($path), 200, [
            'Content-Type' => $type === 'css' ? 'text/css; charset=UTF-8' : 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
