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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nguyên liệu
            $table->string('code')->unique()->nullable(); // Mã nguyên liệu
            $table->text('description')->nullable(); // Mô tả
            $table->string('unit'); // Đơn vị tính (kg, lít, gói, ...)
            $table->decimal('unit_price', 15, 2)->default(0); // Giá mua/đơn vị
            $table->integer('min_stock')->default(0); // Tồn kho tối thiểu
            $table->integer('max_stock')->default(0); // Tồn kho tối đa
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
