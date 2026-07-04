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
        Schema::create('tbl_hasil_clustering', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('tbl_sesi_clustering')->cascadeOnDelete();
            $table->foreignId('kambing_id')->constrained('tbl_kambing')->cascadeOnDelete();
            $table->string('cluster'); // Rendah, Sedang, Tinggi
            $table->decimal('bobot_badan_val', 5, 2);
            $table->integer('tingkat_kelahiran_val');
            $table->decimal('produksi_susu_val', 5, 2);
            $table->double('jarak_c1'); // Jarak ke Centroid 1 (Rendah)
            $table->double('jarak_c2'); // Jarak ke Centroid 2 (Sedang)
            $table->double('jarak_c3'); // Jarak ke Centroid 3 (Tinggi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_hasil_clustering');
    }
};
