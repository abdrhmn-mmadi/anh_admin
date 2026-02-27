<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('role_id')
                  ->after('password')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('region_id')
                  ->after('role_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('phone')->nullable()->after('region_id');
            $table->string('photo')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['region_id']);

            $table->dropColumn([
                'role_id',
                'region_id',
                'phone',
                'photo',
            ]);
        });
    }
};
