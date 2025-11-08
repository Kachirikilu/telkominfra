<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_perjalanans', function (Blueprint $table) {
            $table->id();
            
            // Kolom Foreign Key yang merujuk ke tabel 'perjalanans'
            // 'perjalanan_id' akan merujuk ke 'id' di tabel 'perjalanans'
            // onDelete('cascade') memastikan data di tabel ini ikut terhapus jika induknya (perjalanans) dihapus.
            $table->foreignId('perjalanan_id')->constrained('perjalanans')->onDelete('cascade');

            $table->string('perangkat')->comment('Model perangkat, misal: samsung/SM-G973F');
            $table->string('file_nmf')->nullable();
            $table->enum('status', ['Before', 'After'])->default('Before');  
            $table->timestamp('timestamp_mulai')->nullable();
            $table->timestamp('timestamp_selesai')->nullable();
            // Menambahkan index untuk Foreign Key (opsional, tapi baik untuk performa)
            $table->index(['perjalanan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perjalanans');
    }
};