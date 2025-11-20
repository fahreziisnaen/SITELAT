<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->string('kelas')->primary(); // contoh: X-2, XI-9, XII-10
            $table->string('username')->nullable(); // FK ke users.username (nullable untuk kelas tanpa wali)
            $table->timestamps();

            // Foreign key constraint untuk nullable column
            // Nullable foreign key akan allow null values
            $table->foreign('username')->references('username')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
