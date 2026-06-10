<?php

use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CongThucController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonNhapController;
use App\Http\Controllers\GiaMonController;
use App\Http\Controllers\LoaiMonController;
use App\Http\Controllers\MonController;
use App\Http\Controllers\NguyenLieuController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhaCheController;
use App\Http\Controllers\ResourceAssetController;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiMonitorController;

Route::get('resources-assets/{type}/{file}', ResourceAssetController::class)
    ->whereIn('type', ['css', 'js'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('resource.asset');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [LoginController::class, 'login'])->name('login.submit');
});

// sepay webhook moved to routes/api.php to avoid CSRF checks from web middleware

// Customer-facing checkout (public screen)
Route::get('khach-hang/thanh-toan/{hoaDon}', [PaymentController::class, 'customerCheckout'])
    ->name('customer.checkout');

Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('nguoi-dung', NguoiDungController::class)
        ->parameters(['nguoi-dung' => 'nguoiDung'])
        ->except(['show', 'destroy'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('loai-mon', [LoaiMonController::class, 'index'])
        ->name('loai-mon.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('loai-mon/create', [LoaiMonController::class, 'create'])
        ->name('loai-mon.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('loai-mon', [LoaiMonController::class, 'store'])
        ->name('loai-mon.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('loai-mon/{loaiMon}/edit', [LoaiMonController::class, 'edit'])
        ->name('loai-mon.edit')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::put('loai-mon/{loaiMon}', [LoaiMonController::class, 'update'])
        ->name('loai-mon.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::delete('loai-mon/{loaiMon}', [LoaiMonController::class, 'destroy'])
        ->name('loai-mon.destroy')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('nha-cung-cap', [NhaCungCapController::class, 'index'])
        ->name('nha-cung-cap.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('nha-cung-cap/create', [NhaCungCapController::class, 'create'])
        ->name('nha-cung-cap.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('nha-cung-cap', [NhaCungCapController::class, 'store'])
        ->name('nha-cung-cap.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('nha-cung-cap/{nhaCungCap}/edit', [NhaCungCapController::class, 'edit'])
        ->name('nha-cung-cap.edit')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::put('nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'update'])
        ->name('nha-cung-cap.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::delete('nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'destroy'])
        ->name('nha-cung-cap.destroy')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('mon', [MonController::class, 'index'])
        ->name('mon.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('mon/create', [MonController::class, 'create'])
        ->name('mon.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('mon', [MonController::class, 'store'])
        ->name('mon.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('mon/{mon}/edit', [MonController::class, 'edit'])
        ->name('mon.edit')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::put('mon/{mon}', [MonController::class, 'update'])
        ->name('mon.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::patch('mon/{mon}/doi-trang-thai', [MonController::class, 'toggleStatus'])
        ->name('mon.toggle-status')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::delete('mon/{mon}', [MonController::class, 'destroy'])
        ->name('mon.destroy')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('gia-mon', [GiaMonController::class, 'index'])
        ->name('gia-mon.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('gia-mon/{mon}', [GiaMonController::class, 'show'])
        ->name('gia-mon.show')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('gia-mon/{mon}/create', [GiaMonController::class, 'create'])
        ->name('gia-mon.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('gia-mon/{mon}', [GiaMonController::class, 'store'])
        ->name('gia-mon.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('nguyen-lieu', [NguyenLieuController::class, 'index'])
        ->name('nguyen-lieu.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('nguyen-lieu/create', [NguyenLieuController::class, 'create'])
        ->name('nguyen-lieu.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('nguyen-lieu', [NguyenLieuController::class, 'store'])
        ->name('nguyen-lieu.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('nguyen-lieu/{nguyenLieu}/edit', [NguyenLieuController::class, 'edit'])
        ->name('nguyen-lieu.edit')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::put('nguyen-lieu/{nguyenLieu}', [NguyenLieuController::class, 'update'])
        ->name('nguyen-lieu.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::delete('nguyen-lieu/{nguyenLieu}', [NguyenLieuController::class, 'destroy'])
        ->name('nguyen-lieu.destroy')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::patch('nguyen-lieu/{nguyenLieu}/ngung-su-dung', [NguyenLieuController::class, 'stopUsing'])
        ->name('nguyen-lieu.stop-using')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::resource('don-nhap', DonNhapController::class)
        ->only(['index', 'show', 'create', 'store'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);

    Route::get('cong-thuc', [CongThucController::class, 'index'])
        ->name('cong-thuc.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('cong-thuc/{mon}', [CongThucController::class, 'show'])
        ->name('cong-thuc.show')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('cong-thuc/{mon}/edit', [CongThucController::class, 'edit'])
        ->name('cong-thuc.edit')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::put('cong-thuc/{mon}', [CongThucController::class, 'update'])
        ->name('cong-thuc.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('order', [OrderController::class, 'index'])
        ->name('order.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::get('order/tao-moi', [OrderController::class, 'create'])
        ->name('order.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::post('order', [OrderController::class, 'store'])
        ->name('order.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::get('order/{hoaDon}/invoice', [OrderController::class, 'invoice'])
        ->name('order.invoice')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);

    Route::get('payment/{hoaDon}', [PaymentController::class, 'checkout'])
        ->name('payment.checkout')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::get('payment/{hoaDon}/status', [PaymentController::class, 'status'])
        ->name('payment.status')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::get('payment/{hoaDon}/history', [PaymentController::class, 'history'])
        ->name('payment.history')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_ORDER);
    Route::post('payment/{hoaDon}/refund', [PaymentController::class, 'refund'])
        ->name('payment.refund')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('pha-che', [PhaCheController::class, 'index'])
        ->name('pha-che.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::get('pha-che/data', [PhaCheController::class, 'data'])
        ->name('pha-che.data')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);
    Route::patch('pha-che/{chiTiet}', [PhaCheController::class, 'update'])
        ->name('pha-che.update')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG.','.NguoiDung::CHUC_VU_NHAN_VIEN_PHA_CHE);

    Route::get('bao-cao', [BaoCaoController::class, 'index'])
        ->name('bao-cao.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('bao-cao/export', [BaoCaoController::class, 'export'])
        ->name('bao-cao.export')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get(
        '/ai-monitor',
        [AiMonitorController::class, 'index']
    )->name('ai-monitor.index');

    Route::post(
        '/ai-monitor/upload',
        [AiMonitorController::class, 'upload']
    )->name('ai-monitor.upload');

    Route::post(
        '/ai-monitor/reset',
        [AiMonitorController::class, 'reset']
    )->name('ai-monitor.reset');
});
