<?php

namespace App\Http\Controllers;

use App\Models\MasterKodeAset;
use Illuminate\Http\Request;

class MasterKodeAsetController extends Controller
{
    public function index()
    {
        $masterKodes = MasterKodeAset::orderBy('kategori')
            ->orderBy('kelompok')
            ->orderBy('jenis')
            ->get();

        return view('master_kode_aset.index', compact('masterKodes'));
    }

    public function create()
    {
        return view('master_kode_aset.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'kelompok' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'kode_prefix' => [
                'required',
                'string',
                'max:255',
                'unique:master_kode_aset,kode_prefix',
                'regex:/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/',
            ],
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_prefix.regex' => 'Format prefix harus berupa angka dipisah titik. Contoh: 03.01.01.07',
            'kode_prefix.unique' => 'Prefix ini sudah digunakan.',
        ]);

        MasterKodeAset::create($validated);

        return redirect()->route('master-kode-aset.index')
            ->with('success', 'Master Kode Aset berhasil ditambahkan.');
    }

    public function edit(MasterKodeAset $masterKodeAset)
    {
        return view('master_kode_aset.edit', compact('masterKodeAset'));
    }

    public function update(Request $request, MasterKodeAset $masterKodeAset)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'kelompok' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'kode_prefix' => [
                'required',
                'string',
                'max:255',
                'unique:master_kode_aset,kode_prefix,' . $masterKodeAset->id,
                'regex:/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/',
            ],
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode_prefix.regex' => 'Format prefix harus berupa angka dipisah titik. Contoh: 03.01.01.07',
            'kode_prefix.unique' => 'Prefix ini sudah digunakan.',
        ]);

        $masterKodeAset->update($validated);

        return redirect()->route('master-kode-aset.index')
            ->with('success', 'Master Kode Aset berhasil diperbarui.');
    }

    public function destroy(MasterKodeAset $masterKodeAset)
    {
        // Cek apakah sudah dipakai oleh barang
        if ($masterKodeAset->barangs()->count() > 0) {
            return back()->with('error', 'Tidak bisa dihapus! Prefix ini sudah digunakan oleh barang inventaris.');
        }

        $masterKodeAset->delete();

        return redirect()->route('master-kode-aset.index')
            ->with('success', 'Master Kode Aset berhasil dihapus.');
    }
}