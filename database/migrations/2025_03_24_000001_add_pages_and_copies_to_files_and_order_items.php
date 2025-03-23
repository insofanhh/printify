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
        // Thêm cột pages vào bảng files
        Schema::table('files', function (Blueprint $table) {
            $table->integer('pages')->default(1)->after('type')->comment('Số trang của file');
        });

        // Thêm cột copies vào bảng order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('copies')->default(1)->after('quantity')->comment('Số bản in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('pages');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('copies');
        });
    }
}; 