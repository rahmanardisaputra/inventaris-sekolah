<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangInventaris extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_aset',
        'lokasi_id',
        'kategori',
        'merek',
        'jenis',
        'harga_perolehan',
        'tanggal_perolehan',
        'kondisi_terkini',
        'status_validasi',
        'catatan_waka',
    ];

    protected $table = 'barang_inventaris';

    // Relasi: Barang berada di satu Lokasi
    public function lokasi()
    {
        return $this->belongsTo(LokasiRuangan::class, 'lokasi_id');
    }

    // Relasi: Barang punya banyak Laporan Pemeliharaan
    public function laporans()
    {
        return $this->hasMany(LaporanPemeliharaan::class, 'barang_id');
    }
}