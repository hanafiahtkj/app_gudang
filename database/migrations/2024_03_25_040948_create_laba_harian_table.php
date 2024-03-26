<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laba', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('penjualan', 12, 2);
            $table->decimal('pengeluaran', 12, 2);
            $table->decimal('laba', 12, 2);
            $table->integer('persen_pemilik');
            $table->integer('persen_karyawan');
            $table->decimal('laba_pemilik', 12, 2);
            $table->decimal('laba_karyawan', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laba_harian');
    }
};
