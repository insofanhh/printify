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
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('paper_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('print_option_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_page', 10, 2);
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Sử dụng tên ngắn hơn cho index
            $table->index(['paper_type_id', 'print_option_id'], 'price_rule_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
    }
}; 