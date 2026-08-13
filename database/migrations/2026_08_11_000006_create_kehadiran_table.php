<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jadwal_ibadah_id')->constrained('jadwal_ibadah')->cascadeOnDelete();
            $table->timestamp('waktu_checkin');
            $table->string('metode_verifikasi')->default('facenet');
            $table->timestamps();

            $table->unique(['user_id', 'jadwal_ibadah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
