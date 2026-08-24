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
        // Tabel Kitab (Books)
        Schema::create('kitab', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Kejadian, Keluaran, dll
            $table->string('singkatan'); // Kej, Kel, dll
            $table->integer('urutan'); // 1-66 untuk urutan Alkitab
            $table->integer('jumlah_pasal'); // Total pasal di kitab ini
            $table->string('testament'); // PL (Perjanjian Lama) atau PB (Perjanjian Baru)
            $table->timestamps();
        });

        // Tabel Pasal (Chapters)
        Schema::create('pasal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitab_id')->constrained('kitab')->cascadeOnDelete();
            $table->integer('nomor_pasal'); // 1, 2, 3, dst
            $table->integer('jumlah_ayat'); // Total ayat di pasal ini
            $table->timestamps();
        });

        // Tabel Ayat (Verses)
        Schema::create('ayat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasal_id')->constrained('pasal')->cascadeOnDelete();
            $table->integer('nomor_ayat'); // 1, 2, 3, dst
            $table->text('isi_ayat'); // Isi teks ayat
            $table->timestamps();
        });

        // Tabel Reading Plans
        Schema::create('reading_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // "1 Tahun Baca Alkitab", "6 Bulan Baca Alkitab"
            $table->string('deskripsi');
            $table->integer('durasi_hari'); // 365 atau 180
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tabel Reading Plan Items (detail bacaan per hari)
        Schema::create('reading_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_plan_id')->constrained('reading_plans')->cascadeOnDelete();
            $table->integer('hari_ke'); // Hari ke-1, ke-2, dst
            $table->json('bacaan'); // [{"kitab_id":1, "pasal_mulai":1, "pasal_selesai":3}, ...]
            $table->timestamps();
        });

        // Tabel User Reading Progress
        Schema::create('user_reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reading_plan_id')->constrained('reading_plans')->cascadeOnDelete();
            $table->integer('hari_terakhir')->default(0); // Hari terakhir yang dibaca
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('selesai')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'reading_plan_id']);
        });

        // Tabel User Reading History (setiap kali membaca ayat/pasal)
        Schema::create('user_reading_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pasal_id')->constrained('pasal')->cascadeOnDelete();
            $table->timestamp('waktu_baca');
            $table->boolean('selesai_baca')->default(false); // Apakah pasal selesai dibaca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reading_history');
        Schema::dropIfExists('user_reading_progress');
        Schema::dropIfExists('reading_plan_items');
        Schema::dropIfExists('reading_plans');
        Schema::dropIfExists('ayat');
        Schema::dropIfExists('pasal');
        Schema::dropIfExists('kitab');
    }
};
