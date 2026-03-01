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
        Schema::create('orderreceiveds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger ('table_id');
            $table->string ('order_id');
            $table->string('cus_id');
            $table->string('itm_code');
            $table->string('itm_qty');
            $table->string('sup_id');
            $table->date('itm_rec_date')->nullable();
            $table->string('itm_res_qty')->nullable();
            $table->string('itm_warranty')->nullable();
            $table->float('itm_unit_price')->nullable();
            $table->float('itm_tot_price')->nullable();
            $table->string('itm_rec_state')->nullable();
            $table->date('itm_inv_date')->nullable();
            $table->string('itm_inv_numer')->nullable();
            $table->date('bill_submit_date')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('bill_state')->default('Pending');
            $table->timestamps();
            $table->foreign('table_id')->references('id')->on('orderms');
            $table->foreign('cus_id')->references('cus_id')->on('customers');
            $table->foreign('itm_code')->references('itm_code')->on('items');
            $table->foreign('sup_id')->references('sup_id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderreceiveds');
    }
};
