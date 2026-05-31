<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->string('ma_thanh_toan', 50)->nullable()->unique()->after('ma_hoa_don');
            $table->timestamp('thoi_gian_thanh_toan')->nullable()->after('thoi_gian_tao');
        });
    }

    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->dropColumn(['ma_thanh_toan', 'thoi_gian_thanh_toan']);
        });
    }
};
