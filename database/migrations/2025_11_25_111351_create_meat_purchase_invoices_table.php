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
        Schema::create('meat_purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();  // رقم الفاتورة
            $table->string('supplier_name')->nullable(); // اسم المورد
            $table->decimal('total_amount', 12, 2);      // المبلغ الإجمالي
            $table->date('purchase_date');               // تاريخ الشراء
            $table->text('notes')->nullable();           // ملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_purchase_invoices');
    }
};
