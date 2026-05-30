<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nguoi_dung')) {
            return;
        }

        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id('ma_nguoi_dung');
            $table->string('ho_ten', 100);
            $table->string('ten_dang_nhap', 50)->unique();
            $table->string('mat_khau', 255);
            $table->enum('chuc_vu', [
                'chu_cua_hang',
                'nhan_vien_order',
                'nhan_vien_pha_che',
            ]);
            $table->enum('trang_thai', ['hoat_dong', 'ngung_hoat_dong'])->default('hoat_dong');

            $table->index(['chuc_vu', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
