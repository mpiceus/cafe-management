<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loai_mon')) {
            Schema::create('loai_mon', function (Blueprint $table) {
                $table->id('ma_loai_mon');
                $table->string('ten_loai_mon', 100)->unique();
            });
        }

        if (! Schema::hasTable('mon')) {
            Schema::create('mon', function (Blueprint $table) {
                $table->id('ma_mon');
                $table->foreignId('ma_loai_mon')
                    ->constrained('loai_mon', 'ma_loai_mon')
                    ->restrictOnDelete();
                $table->string('ten_mon', 100);
                $table->string('mo_ta', 255)->nullable();
                $table->string('hinh_anh', 255)->nullable();
                $table->enum('che_do_phuc_vu', ['ca_hai', 'chi_nong', 'chi_lanh', 'khong_ap_dung']);
                $table->boolean('cho_them_topping')->default(false);
                $table->enum('trang_thai', ['dang_ban', 'dung_ban'])->default('dang_ban');

                $table->index(['ma_loai_mon', 'trang_thai']);
            });

            return;
        }

        DB::statement("UPDATE mon SET che_do_phuc_vu = 'chi_nong' WHERE che_do_phuc_vu = 'nong'");
        DB::statement("UPDATE mon SET che_do_phuc_vu = 'chi_lanh' WHERE che_do_phuc_vu = 'lanh'");
        DB::statement("ALTER TABLE mon MODIFY che_do_phuc_vu ENUM('ca_hai','chi_nong','chi_lanh','khong_ap_dung') NOT NULL DEFAULT 'khong_ap_dung'");
    }

    public function down(): void
    {
        if (Schema::hasTable('mon')) {
            DB::statement("UPDATE mon SET che_do_phuc_vu = 'nong' WHERE che_do_phuc_vu = 'chi_nong'");
            DB::statement("UPDATE mon SET che_do_phuc_vu = 'lanh' WHERE che_do_phuc_vu = 'chi_lanh'");
            DB::statement("ALTER TABLE mon MODIFY che_do_phuc_vu ENUM('ca_hai','nong','lanh','khong_ap_dung') NOT NULL DEFAULT 'khong_ap_dung'");
        }
    }
};
