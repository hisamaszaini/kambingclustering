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
        Schema::create('tbl_sesi_clustering', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->integer('jumlah_cluster')->default(3);
            $table->integer('total_iterasi');
            $table->json('centroid_awal');
            $table->json('centroid_akhir');
            $table->integer('total_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sesi_clustering');
    }
};
