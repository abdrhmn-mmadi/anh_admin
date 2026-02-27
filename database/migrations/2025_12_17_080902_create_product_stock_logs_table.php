<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('product_types')->onDelete('cascade');
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('type', ['addition', 'sale']);
            $table->date('log_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_logs');
    }
};
