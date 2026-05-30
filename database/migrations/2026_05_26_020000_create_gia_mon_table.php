<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gia_mon')) {
            return;
        }

        Schema::create('gia_mon', function (Blueprint $table) {
            $table->id('ma_gia_mon');
            $table->foreignId('ma_mon')
                ->constrained('mon', 'ma_mon')
                ->restrictOnDelete();
            $table->decimal('gia', 10, 2);
            $table->dateTime('ngay_ap_dung')->useCurrent();

            $table->index(['ma_mon', 'ngay_ap_dung']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gia_mon');
    }
};
