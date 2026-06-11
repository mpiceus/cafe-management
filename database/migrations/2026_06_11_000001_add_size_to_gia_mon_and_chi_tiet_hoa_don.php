<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gia_mon') && ! Schema::hasColumn('gia_mon', 'size')) {
            Schema::table('gia_mon', function (Blueprint $table) {
                $table->enum('size', ['S', 'M', 'L'])->default('S')->after('ma_mon');
            });
        }

        if (Schema::hasTable('gia_mon')) {
            DB::statement("
                DELETE gm_old FROM gia_mon gm_old
                INNER JOIN gia_mon gm_new
                    ON gm_old.ma_mon = gm_new.ma_mon
                    AND gm_old.size = gm_new.size
                    AND (
                        gm_old.ngay_ap_dung < gm_new.ngay_ap_dung
                        OR (
                            gm_old.ngay_ap_dung = gm_new.ngay_ap_dung
                            AND gm_old.ma_gia_mon < gm_new.ma_gia_mon
                        )
                    )
            ");

            $primaryColumns = collect(DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'gia_mon'
                    AND CONSTRAINT_NAME = 'PRIMARY'
                ORDER BY ORDINAL_POSITION
            "))->pluck('COLUMN_NAME')->all();

            if ($primaryColumns !== ['ma_mon', 'size']) {
                if (! empty($primaryColumns)) {
                    DB::statement('ALTER TABLE gia_mon MODIFY ma_gia_mon BIGINT UNSIGNED NULL');
                    DB::statement('ALTER TABLE gia_mon DROP PRIMARY KEY');
                }

                DB::statement('ALTER TABLE gia_mon ADD PRIMARY KEY (ma_mon, size)');
            }
        }

        if (Schema::hasTable('chi_tiet_hoa_don') && ! Schema::hasColumn('chi_tiet_hoa_don', 'size')) {
            Schema::table('chi_tiet_hoa_don', function (Blueprint $table) {
                $table->enum('size', ['S', 'M', 'L'])->default('S')->after('ma_mon');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('gia_mon')) {
            $primaryColumns = collect(DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'gia_mon'
                    AND CONSTRAINT_NAME = 'PRIMARY'
                ORDER BY ORDINAL_POSITION
            "))->pluck('COLUMN_NAME')->all();

            if ($primaryColumns === ['ma_mon', 'size']) {
                DB::statement('ALTER TABLE gia_mon DROP PRIMARY KEY');
                DB::statement('ALTER TABLE gia_mon MODIFY ma_gia_mon BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            }
        }

        if (Schema::hasTable('chi_tiet_hoa_don') && Schema::hasColumn('chi_tiet_hoa_don', 'size')) {
            Schema::table('chi_tiet_hoa_don', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }

        if (Schema::hasTable('gia_mon') && Schema::hasColumn('gia_mon', 'size')) {
            Schema::table('gia_mon', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }
    }
};
