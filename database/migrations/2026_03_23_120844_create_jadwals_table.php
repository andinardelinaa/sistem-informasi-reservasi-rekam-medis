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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id('id_jadwal');  

            $table->foreignId('id_klinik')
                  ->constrained('klinik', 'id_klinik')  
                  ->onDelete('cascade');

            $table->foreignId('id_dokter')
                  ->constrained('dokter', 'id_dokter')  
                  ->onDelete('cascade');

            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])
                  ->nullable(false);

            $table->time('jam_mulai')->nullable(false);
            $table->time('jam_selesai')->nullable(false);

            // Constraint: satu dokter di satu klinik hanya boleh punya 1 jadwal per hari
            $table->unique(['id_klinik', 'id_dokter', 'hari'], 'unique_jadwal_per_dokter_klinik_hari');

            // Opsional: tambahkan timestamps (created_at & updated_at)
            $table->timestamps();

            // Opsional: kalau mau bisa hapus soft delete
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};