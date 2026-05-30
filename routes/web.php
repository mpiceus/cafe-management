<?php

use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CongThucController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonNhapController;
use App\Http\Controllers\GiaMonController;
use App\Http\Controllers\MonController;
use App\Http\Controllers\NguyenLieuController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhaCheController;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('sepay/webhook', [PaymentController::class, 'webhook'])
    ->name('sepay.webhook');

Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('nguoi-dung', NguoiDungController::class)
        ->parameters(['nguoi-dung' => 'nguoiDung'])
        ->except(['show', 'destroy'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::patch('mon/{mon}/doi-trang-thai', [MonController::class, 'toggleStatus'])
        ->name('mon.toggle-status')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::resource('mon', MonController::class)
        ->except(['show', 'destroy'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('gia-mon', [GiaMonController::class, 'index'])
        ->name('gia-mon.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::get('gia-mon/{mon}/create', [GiaMonController::class, 'create'])
        ->name('gia-mon.create')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
    Route::post('gia-mon/{mon}', [GiaMonController::class, 'store'])
        ->name('gia-mon.store')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::resource('nguyen-lieu', NguyenLieuController::class)
        ->parameters(['nguyen-lieu' => 'nguyenLieu'])
        ->except(['show', 'destroy'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::resource('don-nhap', DonNhapController::class)
        ->only(['index', 'show', 'create', 'store'])
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);

    Route::get('cong-thuc', [CongThucController::class, 'index'])
        ->name('cong-thuc.index')
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
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
        ->middleware('role:'.NguoiDung::CHUC_VU_CHU_CUA_HANG);
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
});
