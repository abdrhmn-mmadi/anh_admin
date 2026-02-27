<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('region_id');

            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->integer('quantity');
            $table->string('reference')->nullable();

            $table->timestamps();

            // Indexes first
            $table->index(['product_id', 'agent_id', 'region_id']);
            $table->index('batch_id');
            $table->index('type');

            // Foreign keys (explicit)
            $table->foreign('product_id')
                ->references('id')->on('product_types')
                ->cascadeOnDelete();

            $table->foreign('batch_id')
                ->references('id')->on('product_batches')
                ->nullOnDelete();

            $table->foreign('agent_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('region_id')
                ->references('id')->on('regions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
