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
            $table->id(); // kolom id auto increment (primary key)
            $table->string('no_yudicium')->unique()->nullable(); // nomor yudicium, dibuat unik
            $table->string('periode'); // periode yudicium (misalnya "2025/2026")
            $table->integer('fakultas'); // nama fakultas
            $table->integer('prodi'); // nama program studi
            $table->enum('approval_status', ['not_approved', 'approved'])->default('not_approved');
            $table->unsignedBigInteger('approved_by')->nullable(); // optional
            $table->timestamp('approved_at')->nullable(); // optional
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
