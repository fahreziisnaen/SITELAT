<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->string('NIS')->primary();
            $table->string('nama_lengkap');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('kelas')->nullable(); // FK ke kelas.kelas (nullable untuk murid lulus)
            $table->enum('status', ['Aktif', 'Lulus'])->default('Aktif');
            $table->year('tahun_lulus')->nullable();
            $table->timestamps();

            // Foreign key dihapus karena murid lulus tidak punya kelas yang valid di tabel kelas
            // Relasi tetap bisa digunakan dengan where clause
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
