<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguyen_lieu', function (Blueprint $table) {
            if (! Schema::hasColumn('nguyen_lieu', 'duoc_su_dung')) {
                $table->boolean('duoc_su_dung')->default(true)->after('duoc_tuy_chinh');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nguyen_lieu', function (Blueprint $table) {
            if (Schema::hasColumn('nguyen_lieu', 'duoc_su_dung')) {
                $table->dropColumn('duoc_su_dung');
            }
        });
    }
};
