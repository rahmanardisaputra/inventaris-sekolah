<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangInventarisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'master_kode_aset_id' => 'required|exists:master_kode_aset,id',
            'lokasi_id' => 'required|exists:lokasi_ruangan,id',
            'merek' => 'nullable|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'harga_perolehan' => 'required|numeric|min:0',
            'tanggal_perolehan' => 'required|date',
            'kondisi_terkini' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'sumber_dana' => 'nullable|string|max:255',
            'catatan_waka' => 'nullable|string',
        ];
    }
}
