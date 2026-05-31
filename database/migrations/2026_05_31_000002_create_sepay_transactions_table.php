<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepay_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('ma_hoa_don')->nullable();
            $table->string('sepay_id')->unique();
            $table->string('gateway', 100)->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('sub_account', 250)->nullable();
            $table->string('transfer_type', 10)->nullable();
            $table->decimal('amount_in', 20, 2)->default(0);
            $table->decimal('amount_out', 20, 2)->default(0);
            $table->decimal('accumulated', 20, 2)->default(0);
            $table->string('code', 250)->nullable();
            $table->text('transaction_content')->nullable();
            $table->string('reference_number', 255)->nullable();
            $table->text('description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('ma_hoa_don')->references('ma_hoa_don')->on('hoa_don')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepay_transactions');
    }
};
