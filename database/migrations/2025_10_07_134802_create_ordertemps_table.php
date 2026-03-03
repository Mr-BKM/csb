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
        Schema::create('ordertemps', function (Blueprint $table) {
            $table->id();
            $table->string ('order_id');
            $table->string('cus_name');
            $table->string('cus_id');
            $table->string('itm_code');
            $table->decimal('itm_qty', 15, 2)->default(0);
            $table->string('order_typ');
            $table->date('order_date');
            $table->timestamps();
            $table->foreign('itm_code')->references('itm_code')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordertemps');
    }
};
