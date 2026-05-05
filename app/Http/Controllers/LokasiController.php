<?php

namespace App\Http\Controllers;

use App\Models\LokasiRuangan;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasis = LokasiRuangan::orderBy('nama_ruangan')->get();
        return view('lokasi.index', compact('lokasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lokasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|unique:lokasi_ruangan,nama_ruangan|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        LokasiRuangan::create($validated);

        return redirect()->route('lokasi.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LokasiRuangan $lokasi)
    {
        // Tampilkan detail ruangan + daftar barang di dalamnya
        $barangs = $lokasi->barangs()
                    ->where('status_validasi', 'approved')
                    ->orderBy('kategori')
                    ->get();
        
        return view('lokasi.show', compact('lokasi', 'barangs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LokasiRuangan $lokasi)
    {
        return view('lokasi.edit', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LokasiRuangan $lokasi)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|unique:lokasi_ruangan,nama_ruangan,' . $lokasi->id . '|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $lokasi->update($validated);

        return redirect()->route('lokasi.index')->with('success', 'Data ruangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LokasiRuangan $lokasi)
    {
        // Cek apakah ada barang di ruangan ini
        if ($lokasi->barangs()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus ruangan karena masih terdapat barang di dalamnya.');
        }

        $lokasi->delete();

        return redirect()->route('lokasi.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}