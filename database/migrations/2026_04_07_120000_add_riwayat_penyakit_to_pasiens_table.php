<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->text('riwayat_penyakit')->nullable()->after('pekerjaan');
        });
    }

    public function down()
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropColumn('riwayat_penyakit');
        });
    }
};