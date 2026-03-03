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
        Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_id');
        $table->string('cus_id')->nullable();
        $table->string('cus_name')->nullable();
        $table->string('itm_code')->nullable();
        $table->string('itm_name')->nullable();
        $table->string('itm_unit_of_measure')->nullable();
        $table->decimal('itm_qty', 15, 2)->nullable()->default(0);
        $table->decimal('itm_stockinhand', 15, 2)->nullable()->default(0);
        $table->date('order_date')->nullable();
        $table->string('order_typ')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
