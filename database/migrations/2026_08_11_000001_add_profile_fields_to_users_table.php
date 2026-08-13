<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('jemaat')->after('password');
            $table->json('face_embedding')->nullable()->after('role');
            $table->boolean('has_biometric')->default(false)->after('face_embedding');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'face_embedding', 'has_biometric']);
        });
    }
};
