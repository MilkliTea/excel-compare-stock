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
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('stock_code')->nullable();
            $table->string('stock_code_description')->nullable();
            $table->string('sub_stock_code')->nullable();
            $table->string('category')->nullable();
            $table->string('gender')->nullable();
            $table->string('e_category')->nullable();
            $table->string('stock_location')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
