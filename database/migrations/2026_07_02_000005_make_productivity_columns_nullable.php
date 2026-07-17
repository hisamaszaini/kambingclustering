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
        Schema::table('tbl_data_produktivitas', function (Blueprint $table) {
            $table->decimal('bobot_badan', 5, 2)->nullable()->change();
            $table->integer('tingkat_kelahiran')->nullable()->change();
            $table->decimal('produksi_susu', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_data_produktivitas', function (Blueprint $table) {
            $table->decimal('bobot_badan', 5, 2)->nullable(false)->change();
            $table->integer('tingkat_kelahiran')->default(0)->nullable(false)->change();
            $table->decimal('produksi_susu', 5, 2)->default(0.00)->nullable(false)->change();
        });
    }
};
