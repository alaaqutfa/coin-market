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
        Schema::create('product_barcode_logs', function (Blueprint $table) {
            $table->id();
            $table->string('barcode'); // الباركود اللي تم التحقق منه
            $table->boolean('exists')->default(false); // هل موجود بالمنتجات؟
            $table->string('source')->nullable(); // مصدر العملية (api / bulkStore / manual)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // المستخدم إذا متوفر
            $table->timestamps();

            // فهارس للبحث السريع
            $table->index('barcode');
            $table->index('exists');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_barcode_logs');
    }
};
