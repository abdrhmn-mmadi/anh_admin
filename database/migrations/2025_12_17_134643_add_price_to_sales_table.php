<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('quantity')->after('user_id'); // add quantity first
            $table->decimal('unit_price', 10, 2)->after('quantity');
            $table->decimal('total_price', 10, 2)->after('unit_price');
        });

    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_price']);
        });
    }
};
