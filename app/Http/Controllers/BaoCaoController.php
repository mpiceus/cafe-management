<?php

namespace App\Http\Controllers;

use App\Services\BaoCaoService;
use Illuminate\View\View;

class BaoCaoController extends Controller
{
    public function __construct(private readonly BaoCaoService $service) {}

    public function index(): View
    {
        return view('bao-cao.index', $this->service->duLieu());
    }
}
