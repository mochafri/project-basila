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
        Schema::create('mhs_yudiciums', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->integer('fakultas_id');
            $table->integer('prody_id');
            $table->string('name');
            $table->integer('study_period');
            $table->integer('pass_sks');
            $table->decimal('ipk', 3, 2);
            $table->string('predikat')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('yudicium_id')->constrained('yudiciums', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mhs_yudiciums');
    }
};
