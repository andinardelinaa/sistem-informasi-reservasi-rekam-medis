<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id('id_pasien');                    // Primary Key
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            
            $table->string('nama', 100);
            $table->char('nik', 16)->unique();
            $table->enum('jns_kelamin', ['L', 'P']);
            $table->date('tgl_lahir');
            $table->string('no_hp', 20);
            $table->string('alamat', 255);
            $table->string('pekerjaan', 100)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pasien');
    }
};