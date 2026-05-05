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
        Schema::create('barang_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique(); // Kode unik untuk QR Code (Misal: INV-001)
            
            // Relasi ke Lokasi
            $table->foreignId('lokasi_id')->constrained('lokasi_ruangan')->onDelete('cascade');
            
            // Detail Barang
            $table->string('kategori'); // Elektronik, Mebel, Alat Tulis
            $table->string('merek');
            $table->string('jenis'); // Misal: Laptop, Kursi, Spidol
            $table->decimal('harga_perolehan', 15, 2)->default(0);
            $table->date('tanggal_perolehan');
            
            // Status & Kondisi
            $table->enum('kondisi_terkini', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_validasi', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Catatan dari Waka saat validasi
            $table->text('catatan_waka')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_inventaris');
    }
};
