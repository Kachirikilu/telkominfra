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
        Schema::create('jadwal_ceramahs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('judul_ceramah');
            $table->string('nama_ustadz');
            $table->string('gambar')->nullable();
            $table->date('tanggal_ceramah');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->string('tempat_ceramah');
            $table->text('tentang_ceramah')->nullable();
            $table->string('kategori_ceramah')->nullable();
            $table->string('link_streaming')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ceramahs');
    }
};
