<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_gigi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_rm')->constrained('rekam_medis', 'id_rm')->onDelete('cascade');
            $table->string('nomor_gigi', 5);
            $table->string('kondisi_gigi', 50);
            $table->text('tindakan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_gigi');
    }
};