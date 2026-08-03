<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id('id_reservasi');
            $table->foreignId('id_pasien')->constrained('pasien', 'id_pasien')->onDelete('cascade');
            $table->foreignId('id_jadwal')->constrained('jadwal', 'id_jadwal')->onDelete('cascade'); 
            $table->date('tgl_reservasi');
            $table->enum('jam_reservasi', [
                '08:00-09:00', '09:00-10:00', '10:00-11:00', 
                '11:00-12:00', '13:00-14:00', '14:00-15:00', 
                '16:00-17:00', '17:00-18:00'
            ]);
            $table->smallInteger('antrean')->default(0);
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'dibatalkan', 'tindakan', 'selesai'])->default('menunggu');
            $table->text('keluhan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};