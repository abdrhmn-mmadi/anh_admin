<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop columns safely
            if (Schema::hasColumn('sales', 'product_id')) {
                // Drop FK if exists
                $sm = DB::select("SELECT CONSTRAINT_NAME 
                                  FROM information_schema.KEY_COLUMN_USAGE
                                  WHERE TABLE_SCHEMA = DATABASE()
                                    AND TABLE_NAME = 'sales'
                                    AND COLUMN_NAME = 'product_id'
                                    AND CONSTRAINT_NAME != 'PRIMARY'");
                if (!empty($sm)) {
                    foreach ($sm as $fk) {
                        $table->dropForeign($fk->CONSTRAINT_NAME);
                    }
                }
                $table->dropColumn(['product_id','quantity','unit_price','total_price']);
            }

            // Add new columns
            $table->enum('invoice_type', ['proforma', 'facture'])->after('customer_address');
            $table->decimal('grand_total', 12, 2)->default(0)->after('invoice_type');
        });
    }

    public function down()
    {
        //
    }
};
