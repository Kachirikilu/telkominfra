<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('pengukuran_sinyals', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel perjalanan_datas
            $table->foreignId('data_perjalanan_id')
                  ->constrained('data_perjalanans')
                  ->onDelete('cascade');

            // Informasi dasar sinyal
            // Kolom non-string tetap nullable
            $table->timestamp('timestamp_waktu')->nullable();

            // Parameter sinyal LTE
            // Kolom non-string tetap nullable
            // $table->bigInteger('cell_id')->nullable();
            $table->string('cell_id', 50);
            $table->integer('pci')->nullable();
            $table->integer('earfcn')->nullable();

            // Kolom string diubah (nullable dihapus) agar bisa menyimpan string kosong ('')
            // Secara default kolom string di MySQL tidak menerima NULL, jadi bisa menyimpan string kosong
            $table->string('band', 50);
            $table->string('frekuensi', 50);
            $table->string('bandwidth', 50);
            $table->string('n_value', 50);

            // Nilai pengukuran (sinyal)
            // Kolom non-string tetap nullable
            $table->float('rsrp')->nullable();
            $table->float('rssi')->nullable();
            $table->float('rsrq')->nullable();
            $table->float('sinr')->nullable();

            // Lokasi
            // Kolom non-string tetap nullable
            $table->double('latitude', 10, 6)->nullable();
            $table->double('longitude', 10, 6)->nullable();
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengukuran_sinyals');
    }
};