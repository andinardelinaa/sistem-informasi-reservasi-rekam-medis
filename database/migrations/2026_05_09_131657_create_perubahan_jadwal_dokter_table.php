<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perubahan_jadwal_dokter', function (Blueprint $table) {
            $table->id('id_perubahan');
            $table->unsignedBigInteger('dokter_id');
            $table->unsignedBigInteger('dokter_pengganti_id')->nullable();
            $table->date('tanggal');
            $table->enum('jenis_perubahan', ['libur', 'pengganti', 'tambahan']);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();

            $table->foreign('dokter_id')->references('id_dokter')->on('dokter')->onDelete('cascade');
            $table->foreign('dokter_pengganti_id')->references('id_dokter')->on('dokter')->onDelete('set null');
            
            $table->index(['dokter_id', 'tanggal']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perubahan_jadwal_dokter');
    }
};