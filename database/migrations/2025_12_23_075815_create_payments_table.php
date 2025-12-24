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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // employee being paid
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            // bank used for payment
            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnDelete();

            // extra bonus only
            $table->decimal('bonus', 10, 2)->default(0);

            // salary (from employees) + bonus
            $table->decimal('total_amount', 10, 2);

            $table->enum('payment_type', ['Salary', 'Bonus'])->default('Salary');

            $table->date('payment_date');
            $table->string('reference')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
