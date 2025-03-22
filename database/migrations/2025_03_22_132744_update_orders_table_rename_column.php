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
        Schema::table('orders', function (Blueprint $table) {
            // Kiểm tra xem cột total_price có tồn tại không
            if (Schema::hasColumn('orders', 'total_price')) {
                $table->renameColumn('total_price', 'total_amount');
            } else if (!Schema::hasColumn('orders', 'total_amount')) {
                // Nếu cả hai cột đều không tồn tại, thêm mới cột total_amount
                $table->decimal('total_amount', 10, 2)->default(0);
            }

            // Kiểm tra xem cột notes có tồn tại không
            if (Schema::hasColumn('orders', 'notes')) {
                $table->renameColumn('notes', 'special_instructions');
            } else if (!Schema::hasColumn('orders', 'special_instructions')) {
                // Nếu cả hai cột đều không tồn tại, thêm mới cột special_instructions
                $table->text('special_instructions')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Đảo ngược các thay đổi nếu cần
            if (Schema::hasColumn('orders', 'total_amount')) {
                $table->renameColumn('total_amount', 'total_price');
            }

            if (Schema::hasColumn('orders', 'special_instructions')) {
                $table->renameColumn('special_instructions', 'notes');
            }
        });
    }
};
