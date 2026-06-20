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

            $table->foreignId('master_kode_aset_id')
                ->constrained('master_kode_aset')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('kode_aset')->unique();

            $table->foreignId('lokasi_id')
                ->constrained('lokasi_ruangan')
                ->cascadeOnDelete();

            $table->string('merek')->nullable();
            $table->string('nama_barang');

            $table->decimal('harga_perolehan', 15, 2)->default(0);
            $table->date('tanggal_perolehan');

            $table->enum('kondisi_terkini', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat'
            ])->default('Baik');

            $table->enum('status_validasi', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

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
