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
        Schema::table('product_barcode_logs', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_barcode_logs', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
