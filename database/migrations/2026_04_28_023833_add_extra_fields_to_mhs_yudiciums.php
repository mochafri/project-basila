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
        Schema::table('mhs_yudiciums', function (Blueprint $table) {
            $table->string('id_smt_masuk')->nullable()->after('nim');
            $table->string('tmp_lahir')->nullable()->after('name');
            $table->date('tgl_lahir')->nullable()->after('tmp_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mhs_yudiciums', function (Blueprint $table) {
            $table->dropColumn(['id_smt_masuk', 'tmp_lahir', 'tgl_lahir']);
        });
    }
};
