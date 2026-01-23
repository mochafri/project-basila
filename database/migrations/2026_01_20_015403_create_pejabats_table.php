<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pejabats', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('fakultas_id')->nullable();

            $table->string('nama');
            $table->string('gelar')->nullable();
            $table->string('jabatan');

            $table->enum('level', ['universitas', 'fakultas', 'prodi']);

            $table->boolean('aktif')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pejabats');
    }
};

