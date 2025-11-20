<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keterlambatan', function (Blueprint $table) {
            $table->id('id');
            $table->string('NIS')->nullable(); // Snapshot NIS (tidak ada FK constraint agar tetap tersimpan sebagai snapshot)
            $table->string('nama_murid')->nullable(); // Snapshot nama murid saat keterlambatan terjadi
            $table->string('gender')->nullable(); // Snapshot gender/jenis kelamin saat keterlambatan terjadi
            $table->string('kelas')->nullable(); // Snapshot kelas saat keterlambatan terjadi
            $table->string('username')->nullable(); // Snapshot walikelas saat keterlambatan terjadi
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->timestamps();

            // Hanya foreign key untuk username (walikelas), NIS tidak ada FK constraint agar snapshot tetap tersimpan
            $table->foreign('username')->references('username')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keterlambatan');
    }
};
