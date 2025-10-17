<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('yudiciums', function (Blueprint $table) {
            $table->id();
            $table->string('no_yudicium')->unique()->nullable();
            $table->date('periode');
            $table->integer('fakultas_id');
            $table->integer('prodi_id');
            $table->enum('approval_status', ['Waiting', 'Approved','Rejected'])->default('Waiting');
            $table->string('catatan')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudiciums');
    }
};
