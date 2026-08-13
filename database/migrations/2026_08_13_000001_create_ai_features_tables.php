<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->json('minat_tags');
            $table->json('bakat_tags');
            $table->unsignedTinyInteger('min_usia')->default(17);
            $table->unsignedTinyInteger('max_usia')->default(65);
            $table->json('hari_pelayanan');
            $table->string('icon')->default('fa-hands');
            $table->timestamps();
        });

        Schema::create('cool_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pemimpin');
            $table->string('kecamatan');
            $table->string('lokasi');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('jadwal');
            $table->unsignedTinyInteger('min_usia')->default(17);
            $table->unsignedTinyInteger('max_usia')->default(60);
            $table->json('tema_minat');
            $table->unsignedSmallInteger('kapasitas')->default(15);
            $table->unsignedSmallInteger('terisi')->default(0);
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('usia')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('alamat')->nullable();
            $table->json('minat')->nullable();
            $table->json('bakat')->nullable();
            $table->json('ketersediaan_hari')->nullable();
            $table->enum('ketersediaan_waktu', ['pagi', 'siang', 'sore', 'malam', 'fleksibel'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_pelayanan_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tim_pelayanan_id')->constrained('tim_pelayanan')->cascadeOnDelete();
            $table->decimal('score', 5, 4)->nullable();
            $table->string('source')->default('rekomendasi');
            $table->timestamps();
            $table->unique(['user_id', 'tim_pelayanan_id']);
        });

        Schema::create('user_cool_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cool_group_id')->constrained('cool_groups')->cascadeOnDelete();
            $table->decimal('score', 5, 4)->nullable();
            $table->string('status')->default('rekomendasi');
            $table->timestamps();
            $table->unique(['user_id', 'cool_group_id']);
        });

        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->json('keywords')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->json('sources')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('user_cool_choices');
        Schema::dropIfExists('user_pelayanan_choices');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('cool_groups');
        Schema::dropIfExists('tim_pelayanan');
    }
};
