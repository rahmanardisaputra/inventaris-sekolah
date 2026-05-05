<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiRuangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_ruangan',
        'penanggung_jawab',
    ];

    protected $table = 'lokasi_ruangan';

    // Relasi: Satu Lokasi punya banyak Barang
    public function barangs()
    {
        return $this->hasMany(BarangInventaris::class, 'lokasi_id');
    }
}