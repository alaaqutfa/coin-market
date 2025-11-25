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
        Schema::create('meat_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // فخذ بقر، صدر دجاج، إلخ
            $table->string('image')->nullable(); // ✅ الصورة
            $table->text('description')->nullable();
            $table->decimal('current_stock', 10, 3)->default(0);   // الوزن الحالي
            $table->decimal('cost_price', 10, 2)->default(0);      // سعر التكلفة للكيلو
            $table->decimal('selling_price', 10, 2)->default(0);   // سعر المبيع للكيلو
            $table->decimal('waste_percentage', 5, 2)->default(0); // نسبة الهدر %
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_products');
    }
};
