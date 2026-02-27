<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob'); // Date of birth
            $table->string('address');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->decimal('salary', 10, 2);
            $table->string('account_number')->nullable();

            // Foreign keys
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();

            $table->enum('contract_type', ['CDD', 'CDI', 'Stagiaire']);
            $table->date('date_recruited');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
