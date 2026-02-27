<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->foreignId('department_id')
                ->after('region_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->after('department_id')
                ->constrained()
                ->cascadeOnDelete();

            // position as a simple column
            $table->string('position')
                ->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropForeign(['department_id']);
            $table->dropForeign(['service_id']);

            $table->dropColumn([
                'department_id',
                'service_id',
                'position'
            ]);
        });
    }
};

