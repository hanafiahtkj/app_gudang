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
        Schema::create('stock_reduction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_reduction_id');
            $table->foreignId('product_id');
            $table->decimal('quantity', 12, 2);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reduction_details');
    }
};
