<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPemeliharaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'pelapor_id',
        'deskripsi_kerusakan',
        'foto_bukti_awal',
        'foto_bukti_akhir',
        'status_laporan',
        'tindakan_waka',
        'biaya_estimasi',
        'tanggal_selesai',
    ];

    protected $table = 'laporan_pemeliharaan';

    // Relasi: Laporan milik satu Barang
    public function barang()
    {
        return $this->belongsTo(BarangInventaris::class, 'barang_id');
    }

    // Relasi: Laporan dibuat oleh satu User (Pelapor)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}