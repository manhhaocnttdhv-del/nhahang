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
        Schema::create('ingredient_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['import', 'export', 'adjustment']); // Nhập, xuất, điều chỉnh
            $table->decimal('quantity', 10, 2); // Số lượng
            $table->decimal('unit_price', 15, 2)->default(0); // Giá/đơn vị
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng tiền
            $table->date('stock_date'); // Ngày nhập/xuất
            $table->text('notes')->nullable(); // Ghi chú
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_stocks');
    }
};
