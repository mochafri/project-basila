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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('STUDENTID')->unique();
            $table->string('FULLNAME');
            $table->string('MASA_STUDI');
            $table->integer('PASS_CREDIT');
            $table->decimal('GPA', 3, 2);
            $table->string('STATUS')->nullable();
            $table->integer('STUDYPROGRAMID');
            $table->integer('FACULTYID');
            $table->string('PREDIKAT')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};