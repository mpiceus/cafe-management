<?php

namespace App\Providers;

use App\Repositories\BaoCaoRepository;
use App\Repositories\CongThucRepository;
use App\Repositories\Contracts\GiaMonRepositoryInterface;
use App\Repositories\Contracts\BaoCaoRepositoryInterface;
use App\Repositories\Contracts\CongThucRepositoryInterface;
use App\Repositories\Contracts\DonNhapRepositoryInterface;
use App\Repositories\Contracts\LoaiMonRepositoryInterface;
use App\Repositories\Contracts\NguyenLieuRepositoryInterface;
use App\Repositories\Contracts\NhaCungCapRepositoryInterface;
use App\Repositories\Contracts\NguoiDungRepositoryInterface;
use App\Repositories\Contracts\MonRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PhaCheRepositoryInterface;
use App\Repositories\DonNhapRepository;
use App\Repositories\GiaMonRepository;
use App\Repositories\LoaiMonRepository;
use App\Repositories\MonRepository;
use App\Repositories\NguyenLieuRepository;
use App\Repositories\NhaCungCapRepository;
use App\Repositories\NguoiDungRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PhaCheRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaoCaoRepositoryInterface::class, BaoCaoRepository::class);
        $this->app->bind(CongThucRepositoryInterface::class, CongThucRepository::class);
        $this->app->bind(DonNhapRepositoryInterface::class, DonNhapRepository::class);
        $this->app->bind(GiaMonRepositoryInterface::class, GiaMonRepository::class);
        $this->app->bind(LoaiMonRepositoryInterface::class, LoaiMonRepository::class);
        $this->app->bind(MonRepositoryInterface::class, MonRepository::class);
        $this->app->bind(NguyenLieuRepositoryInterface::class, NguyenLieuRepository::class);
        $this->app->bind(NhaCungCapRepositoryInterface::class, NhaCungCapRepository::class);
        $this->app->bind(NguoiDungRepositoryInterface::class, NguoiDungRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PhaCheRepositoryInterface::class, PhaCheRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
        }
    }
}
