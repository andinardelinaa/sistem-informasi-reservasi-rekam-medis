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
        Schema::table('jadwal', function (Blueprint $table) {
            $table->unsignedTinyInteger('bulan')->nullable()->after('hari');
            $table->unsignedSmallInteger('tahun')->nullable()->after('bulan');
            $table->boolean('is_aktif')->default(true)->after('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'tahun', 'is_aktif']);
        });
    }
};
