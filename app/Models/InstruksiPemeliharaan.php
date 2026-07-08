<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstruksiPemeliharaan extends Model
{
    protected $fillable = ['master_kode_aset_id', 'instruksi'];

    public function masterKodeAset()
    {
        return $this->belongsTo(MasterKodeAset::class);
    }
}
