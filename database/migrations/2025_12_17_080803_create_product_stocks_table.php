<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('product_types')->onDelete('cascade');
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['agent_id', 'product_id', 'region_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
