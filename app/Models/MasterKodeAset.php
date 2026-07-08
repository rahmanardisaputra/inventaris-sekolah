<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKodeAset extends Model
{
    use HasFactory;

    protected $table = 'master_kode_aset';

    protected $fillable = [
        'kategori',
        'kelompok',
        'jenis',
        'kode_prefix',
        'keterangan',
    ];

    public function barangInventaris()
    {
        return $this->hasMany(BarangInventaris::class, 'master_kode_aset_id');
    }

    public function instruksiPemeliharaans()
    {
        return $this->hasMany(InstruksiPemeliharaan::class, 'master_kode_aset_id');
    }
}
