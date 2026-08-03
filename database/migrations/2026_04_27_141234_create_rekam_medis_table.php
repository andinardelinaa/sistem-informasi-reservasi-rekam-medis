<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id('id_rm');
            $table->foreignId('id_dokter')->constrained('dokter', 'id_dokter')->onDelete('cascade');
            $table->foreignId('id_reservasi')->constrained('reservasi', 'id_reservasi')->onDelete('cascade');
            $table->date('tgl_periksa');
            $table->text('diagnosa');
            $table->text('tindakan')->nullable(); // tindakan utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};