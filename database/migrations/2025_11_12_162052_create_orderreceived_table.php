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
            $table->decimal('itm_qty', 15, 2)->default(0);
            $table->string('sup_id');
            $table->date('itm_rec_date')->nullable();
            $table->decimal('itm_res_qty', 15, 2)->nullable()->default(0);
            $table->string('itm_warranty')->nullable();
            $table->decimal('itm_unit_price', 15, 2)->nullable()->default(0);
            $table->decimal('itm_tot_price', 15, 2)->nullable()->default(0);
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
