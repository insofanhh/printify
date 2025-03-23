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
        // File này trống và không cần thiết nữa vì đã xử lý trong migration chính
        // Giữ lại để tránh lỗi với các môi trường đã chạy migration này
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần thực hiện gì vì migration này đã không còn cần thiết
    }
};
