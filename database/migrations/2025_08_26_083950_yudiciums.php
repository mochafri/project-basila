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
        Schema::create('yudiciums', function (Blueprint $table) {
            $table->id(); // kolom id auto increment (primary key)
            $table->string('no_yudicium')->unique(); // nomor yudicium, dibuat unik
            $table->time('periode'); // periode yudicium (misalnya "2025/2026")
            $table->string('fakultas'); // nama fakultas
            $table->string('prodi'); // nama program studi
            $table->timestamps(); // created_at & updated_at
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
