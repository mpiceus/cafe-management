<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loai_mon', function (Blueprint $table) {
            if (! Schema::hasColumn('loai_mon', 'mo_ta')) {
                $table->string('mo_ta', 255)->nullable()->after('ten_loai_mon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loai_mon', function (Blueprint $table) {
            if (Schema::hasColumn('loai_mon', 'mo_ta')) {
                $table->dropColumn('mo_ta');
            }
        });
    }
};
