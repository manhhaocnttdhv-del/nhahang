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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên chi phí (ví dụ: Tiền thuê mặt bằng, Điện nước, Marketing...)
            $table->string('category')->nullable(); // Loại chi phí (rent, utilities, marketing, equipment, other)
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->decimal('amount', 15, 2); // Số tiền
            $table->date('expense_date'); // Ngày phát sinh chi phí
            $table->string('payment_method')->nullable(); // Phương thức thanh toán (cash, bank_transfer, etc.)
            $table->string('receipt_number')->nullable(); // Số hóa đơn/chứng từ
            $table->string('receipt_file')->nullable(); // File hóa đơn (nếu có)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Người tạo
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

