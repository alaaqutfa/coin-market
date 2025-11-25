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
        Schema::create('meat_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meat_product_id')->constrained('meat_products');
            $table->enum('movement_type', ['in', 'out', 'return', 'waste']); // ✅ أضفنا waste
            $table->decimal('quantity', 10, 3);                              // الكمية بالكيلو
            $table->decimal('unit_price', 10, 2);                            // سعر البيع للكيلو
            $table->decimal('total_price', 10, 2);                           // الكمية × السعر
            $table->date('movement_date');
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_inventory_movements');
    }
};
