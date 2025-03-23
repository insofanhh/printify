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
        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
                $table->string('path');
                $table->string('name');
                $table->bigInteger('size');
                $table->string('type')->nullable();
                $table->boolean('is_processed')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không xóa bảng files nếu nó được tạo từ migration trước đó
        // Schema::dropIfExists('files');
    }
};
