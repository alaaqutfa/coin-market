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
        Schema::table('products', function (Blueprint $table) {
            $table->string('symbol', 5)->default('$')->after('price');
            $table->unsignedBigInteger('category_id')->nullable()->after('symbol');
            $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');

            // العلاقات (في حال أنشأنا الجداول الجديدة)
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['symbol', 'category_id', 'brand_id']);
        });
    }
};
