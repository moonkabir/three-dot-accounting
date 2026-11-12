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
        Schema::create('customer_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_bill_id')->constrained('customer_bills')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0); // for profit calculation
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_bill_items');
    }
};

