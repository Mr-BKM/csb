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
        Schema::create('issuings', function (Blueprint $table) {
            $table->id();
            $table->string ('issue_id');
            $table->string('issue_typ');
            $table->string('cus_name');
            $table->string('cus_id');
            $table->string('itm_code');
            $table->decimal('itm_stockinhand', 10, 2);
            $table->decimal('itm_qty', 10, 2);
            $table->date('issue_date');
            $table->timestamps();
            $table->foreign('itm_code')->references('itm_code')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuings');
    }
};
