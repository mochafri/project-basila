<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mhs_yudiciums', function (Blueprint $table) {
            $table->string('status_otomatis')->nullable()->after('predikat');
            $table->text('alasan_status')->nullable()->after('status_otomatis');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('status_otomatis')->nullable()->after('predikat');
            $table->text('alasan_status')->nullable()->after('status_otomatis');
        });
    }

    public function down(): void
    {
        Schema::table('mhs_yudiciums', function (Blueprint $table) {
            $table->dropColumn('status_otomatis');
            $table->dropColumn('alasan_status');
        });
    }
};
