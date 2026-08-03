<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikels', function (Blueprint $table) {
            $table->id('id_artikel'); // primary key sesuai permintaan
            $table->string('judul', 255);
            $table->text('isi');
            $table->string('gambar')->nullable(); // path gambar
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // foreign key ke users
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};