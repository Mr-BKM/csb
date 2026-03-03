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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('itm_code')->unique();
            $table->string('itm_barcode')->unique()->nullable();
            $table->string('itm_name');
            $table->string('itm_sinhalaname')->nullable();
            $table->string('itm_book_code');
            $table->string('itm_page_num');
            $table->string('itm_group_id');
            $table->string('itm_group');
            $table->string('itm_subgroup_id')->nullable();
            $table->string('itm_subgroup')->nullable();
            $table->string('itm_unit_of_measure');
            $table->decimal('itm_book_stock', 10, 2)->default(0);
            $table->decimal('itm_loan_stock', 10, 2)->default(0);
            $table->decimal('itm_stock', 10, 2)->default(0);
            $table->decimal('itm_reorder_level', 10, 2)->default(0);
            $table->string('itm_reorder_flag');
            $table->text('itm_description')->nullable();
            $table->string('itm_status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
