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
        Schema::create('tbl_data_produktivitas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kambing_id');
            $table->foreign('kambing_id')->references('id')->on('tbl_kambing')->cascadeOnDelete();
            $table->date('tanggal_pencatatan');
            $table->decimal('bobot_badan', 5, 2);
            $table->integer('tingkat_kelahiran')->default(0);
            $table->decimal('produksi_susu', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_data_produktivitas');
    }
};
