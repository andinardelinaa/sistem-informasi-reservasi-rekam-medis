<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notif');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('reservasi'); // reservasi, jadwal, umum
            $table->string('url')->nullable(); // link saat diklik
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};