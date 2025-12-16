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
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm trường session (buổi) thay vì booking_time và end_time
            $table->enum('session', ['morning', 'lunch', 'afternoon', 'dinner'])->nullable()->after('booking_date');
            // Giữ lại booking_time và end_time để tương thích ngược, nhưng sẽ dùng session chủ yếu
            // Có thể xóa sau khi migrate xong dữ liệu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('session');
        });
    }
};
