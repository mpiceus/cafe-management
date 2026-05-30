<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepay_refunds', function (Blueprint $table) {
            $table->id();
            $table->integer('ma_hoa_don');
            $table->unsignedBigInteger('sepay_transaction_id')->nullable();
            $table->decimal('amount', 20, 2);
            $table->string('status', 30)->default('requested');
            $table->string('reason', 255)->nullable();
            $table->json('response')->nullable();
            $table->timestamps();

            $table->foreign('ma_hoa_don')->references('ma_hoa_don')->on('hoa_don')->cascadeOnDelete();
            $table->foreign('sepay_transaction_id')->references('id')->on('sepay_transactions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepay_refunds');
    }
};
