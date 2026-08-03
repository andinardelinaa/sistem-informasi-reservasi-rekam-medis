<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klinik', function (Blueprint $table) {
            $table->id('id_klinik');
            $table->string('nama_klinik');
            $table->text('alamat');
            $table->string('maps_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klinik');
    }
};