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
        Schema::create('issuingloans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('issue_table_id');
            $table->string ('issue_id');
            $table->string('cus_id');
            $table->string('itm_code');
            $table->decimal('itm_stockinhand', 10, 2); 
            $table->decimal('itm_qty', 10, 2);
            $table->date('issue_date');
            $table->string('issue_typ');
            $table->timestamps();
            $table->foreign('itm_code')->references('itm_code')->on('items');
            $table->foreign('cus_id')->references('cus_id')->on('customers');
            $table->foreign('issue_table_id')->references('id')->on('issuings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuingloans');
    }
};
