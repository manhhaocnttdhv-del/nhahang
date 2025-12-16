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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('employment_type', ['full_time', 'part_time'])->default('full_time');
            $table->date('period_start'); // Ngày bắt đầu kỳ lương
            $table->date('period_end');   // Ngày kết thúc kỳ lương
            $table->decimal('base_salary', 15, 2)->default(0); // Lương cơ bản
            $table->integer('working_days')->default(0); // Số ngày làm việc (full-time)
            $table->integer('working_hours')->default(0); // Số giờ làm việc (part-time)
            $table->decimal('hourly_rate', 10, 2)->default(0); // Lương theo giờ (part-time)
            $table->decimal('overtime_hours', 8, 2)->default(0); // Giờ làm thêm
            $table->decimal('overtime_rate', 10, 2)->default(0); // Lương làm thêm/giờ
            $table->decimal('bonus', 15, 2)->default(0); // Thưởng
            $table->decimal('deduction', 15, 2)->default(0); // Khấu trừ
            $table->decimal('total_salary', 15, 2)->default(0); // Tổng lương
            $table->text('notes')->nullable(); // Ghi chú
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
