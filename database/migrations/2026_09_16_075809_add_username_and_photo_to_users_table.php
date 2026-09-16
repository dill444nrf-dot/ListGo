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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom username (boleh kosong, tapi harus unik jika diisi)
            $table->string('username')->nullable()->unique()->after('email');
            
            // Tambahkan kolom photo untuk menyimpan path/lokasi file gambar
            $table->string('photo')->nullable()->after('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'photo']);
        });
    }
};