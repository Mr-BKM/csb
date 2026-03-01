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
        Schema::create('orderms', function (Blueprint $table) {
            $table->id();
            $table->string ('order_id');
            $table->string('cus_name');
            $table->string('cus_id');
            $table->string('itm_code');
            $table->string('itm_qty');
            $table->string('order_typ');
            $table->date('order_date');
            $table->string('po_date')->nullable();
            $table->string('po_number')->nullable();
            $table->string('sup_id')->nullable();
            $table->string('sup_name')->nullable();
            $table->string('po_state')->default('Pending');
            $table->date('itm_rec_date')->nullable();
            $table->string('itm_inv_numer')->nullable();
            $table->string('itm_res_qty')->nullable();
            $table->string('itm_warranty')->nullable();
            $table->float('itm_unit_price')->nullable();
            $table->float('itm_tot_price')->nullable();
            $table->float('inv_tot_price')->nullable();
            $table->string('itm_rec_state')->default('Pending');
            $table->string('bill_submit_date')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('bill_state')->default('Pending');
            $table->timestamps();
            $table->foreign('itm_code')->references('itm_code')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderms');
    }
};
