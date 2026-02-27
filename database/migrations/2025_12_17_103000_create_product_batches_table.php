<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('product_types')
                ->cascadeOnDelete();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('region_id')
                ->constrained('regions')
                ->cascadeOnDelete();

            $table->integer('quantity_available');

            // Optional but powerful
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
