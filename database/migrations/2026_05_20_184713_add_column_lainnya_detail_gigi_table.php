<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_gigi', function (Blueprint $table) {
            $table->string('kondisi_lainnya', 255)->nullable()->after('kondisi_gigi');
            $table->string('tindakan_lainnya', 255)->nullable()->after('tindakan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_gigi', function (Blueprint $table) {
            $table->dropColumn(['kondisi_lainnya', 'tindakan_lainnya']);
        });
    }
};