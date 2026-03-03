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
            $table->decimal('itm_qty', 15, 2)->default(0);
            $table->string('order_typ');
            $table->date('order_date');
            $table->string('po_date')->nullable();
            $table->string('po_number')->nullable();
            $table->string('sup_id')->nullable();
            $table->string('sup_name')->nullable();
            $table->string('po_state')->default('Pending');
            $table->date('itm_rec_date')->nullable();
            $table->string('itm_inv_numer')->nullable();
            $table->decimal('itm_res_qty', 15, 2)->nullable()->default(0);
            $table->string('itm_warranty')->nullable();
            $table->decimal('itm_unit_price', 15, 2)->nullable()->default(0);
            $table->decimal('itm_tot_price', 15, 2)->nullable()->default(0);
            $table->decimal('inv_tot_price', 15, 2)->nullable()->default(0);
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
