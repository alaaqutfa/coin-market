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
        Schema::create('meat_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('meat_purchase_invoices')->onDelete('cascade');
            $table->foreignId('meat_product_id')->constrained('meat_products');
            $table->decimal('quantity', 10, 3);   // الكمية بالكيلو
            $table->decimal('unit_cost', 10, 2);  // سعر التكلفة للكيلو
            $table->decimal('total_cost', 10, 2); // الإجمالي (يحسب تلقائياً)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_purchase_items');
    }
};
