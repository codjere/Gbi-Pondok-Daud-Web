<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konten_warta', function (Blueprint $table) {
            // Path relatif gambar pada disk "public" (mis. warta/abc.jpg). Nullable
            // karena warta/renungan boleh tanpa gambar.
            $table->string('gambar')->nullable()->after('isi');
        });
    }

    public function down(): void
    {
        Schema::table('konten_warta', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
