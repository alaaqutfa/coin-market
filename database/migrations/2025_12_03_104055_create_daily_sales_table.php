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
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meat_product_id')
                  ->constrained('meat_products')
                  ->onDelete('cascade');
            $table->date('sale_date'); // تاريخ البيع
            $table->decimal('sale_price', 12, 2)->default(0); // سعر البيع
            $table->decimal('return_price', 12, 2)->default(0); // سعر الإرجاع
            $table->enum('transaction_type', ['sale', 'return'])->default('sale'); // نوع العملية
            $table->decimal('quantity', 10, 3)->default(0); // الكمية بالكيلو
            $table->decimal('total_amount', 12, 2)->default(0); // المبلغ الإجمالي
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamp('transaction_time')->useCurrent(); // وقت العملية تلقائياً
            $table->timestamps();

            // فهارس للتحسين
            $table->index('sale_date');
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales');
    }
};
