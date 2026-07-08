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
        Schema::create('instruksi_pemeliharaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_kode_aset_id')->constrained('master_kode_aset')->cascadeOnDelete();
            $table->string('instruksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruksi_pemeliharaans');
    }
};
