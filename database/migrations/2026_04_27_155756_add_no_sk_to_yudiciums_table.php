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
        Schema::table('yudiciums', function (Blueprint $table) {
            $table->string('no_sk')->nullable()->after('no_yudicium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudiciums', function (Blueprint $table) {
            $table->dropColumn('no_sk');
        });
    }
};
