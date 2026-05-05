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
        Schema::create('laporan_pemeliharaan', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Barang dan User Pelapor
            $table->foreignId('barang_id')->constrained('barang_inventaris')->onDelete('cascade');
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            
            // Detail Laporan
            $table->text('deskripsi_kerusakan');
            $table->string('foto_bukti_awal')->nullable(); // Foto saat rusak
            $table->string('foto_bukti_akhir')->nullable(); // Foto setelah diperbaiki
            
            // Alur Status
            // draft -> revisi -> validated_staff -> approved_waka -> selesai
            $table->enum('status_laporan', [
                'draft', 
                'revisi', 
                'validated_staff', 
                'approved_waka', 
                'selesai'
            ])->default('draft');
            
            // Tindak Lanjut
            $table->text('tindakan_waka')->nullable(); // Instruksi Waka (Misal: Ganti Sparepart)
            $table->decimal('biaya_estimasi', 15, 2)->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pemeliharaan');
    }
};
