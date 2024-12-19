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
        Schema::table('invoice_product_lines', static function (Blueprint $table): void {
            $table->integer('total_unit_price')
                ->after('price')
                ->default(0)
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_product_lines', static function (Blueprint $table): void {
            $table->dropColumn('total_unit_price');
        });
    }
};
