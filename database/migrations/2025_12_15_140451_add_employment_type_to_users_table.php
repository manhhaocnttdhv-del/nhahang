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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('employment_type', ['full_time', 'part_time'])->default('full_time')->after('role');
            $table->decimal('base_salary', 15, 2)->default(0)->after('employment_type'); // Lương cơ bản/tháng (full-time)
            $table->decimal('hourly_rate', 10, 2)->default(0)->after('base_salary'); // Lương/giờ (part-time)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employment_type', 'base_salary', 'hourly_rate']);
        });
    }
};
