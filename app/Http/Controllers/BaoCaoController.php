<?php

namespace App\Http\Controllers;

use App\Exports\BaoCaoTongHopExport;
use App\Services\BaoCaoService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BaoCaoController extends Controller
{
    public function __construct(private readonly BaoCaoService $service) {}

    public function index(Request $request): View
    {
        return view('bao-cao.index', $this->service->duLieu($request->only(['tu_ngay', 'den_ngay'])));
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['tu_ngay', 'den_ngay']);
        $data = $this->service->duLieu($filters);
        $fileName = 'bao-cao-'.($filters['tu_ngay'] ?? 'tu').'-'.($filters['den_ngay'] ?? 'den').'.xlsx';

        return Excel::download(new BaoCaoTongHopExport(
            $data['doanhThuTheoNgay'],
            $data['doanhThuTheoThang'],
            $data['monBanChay']
        ), $fileName);
    }
}
