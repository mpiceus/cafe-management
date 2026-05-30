<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chi_tiet_don_nhap', function (Blueprint $table) {
            if (! Schema::hasColumn('chi_tiet_don_nhap', 'don_vi_mua')) {
                $table->string('don_vi_mua', 10)->nullable()->after('so_luong');
            }

            if (! Schema::hasColumn('chi_tiet_don_nhap', 'so_luong_nhap_kho')) {
                $table->decimal('so_luong_nhap_kho', 10, 2)->nullable()->after('don_vi_mua');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chi_tiet_don_nhap', function (Blueprint $table) {
            if (Schema::hasColumn('chi_tiet_don_nhap', 'so_luong_nhap_kho')) {
                $table->dropColumn('so_luong_nhap_kho');
            }

            if (Schema::hasColumn('chi_tiet_don_nhap', 'don_vi_mua')) {
                $table->dropColumn('don_vi_mua');
            }
        });
    }
};
